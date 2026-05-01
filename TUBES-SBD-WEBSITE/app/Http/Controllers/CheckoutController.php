<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketAvailability;
use App\Models\VisitSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{


    public function checkout(Request $request): RedirectResponse
    {
        // dd('checkout reached');

        $userId  = Auth::id();
        $guestId = session('guest_id');

        if (! $userId && ! $guestId) {
            abort(403, 'User or guest identity not found. Please add items to cart first.');
        }

        $context   = $this->resolveCartContext($request);
        $cart      = $context['cart'];

        if (! $cart || $cart->cartGroups->isEmpty()) {
            return redirect()->route('ticket.cart')->with('error', 'Cart is empty');
        }

        // Validate that all groups have items
        foreach ($cart->cartGroups as $group) {
            if ($group->cartItems->isEmpty()) {
                 return redirect()->route('ticket.cart')->with('error', 'One or more cart groups are empty.');
            }
        }

        // --- IDEMPOTENCY: Reuse existing pending order ---
        $existingOrder = Order::query()
            ->where(function ($query) use ($userId, $guestId) {
                if ($userId) $query->where('user_id', $userId);
                else $query->where('guest_id', $guestId);
            })
            ->where('expired_at', '>', now())
            ->whereHas('payment', fn($q) => $q->where('payment_status', 'Pending'))
            ->latest('order_date')
            ->first();

        if ($existingOrder) {
            return redirect()->route('checkout.payments', $existingOrder->order_id);
        }

        try {
            $order = DB::transaction(function () use ($userId, $guestId, $cart) {
                $cart->lockForUpdate();
                $cartItems = $cart->cartGroups->flatMap->cartItems;

                $orderTotalAmount = 0.0;
                foreach ($cartItems as $item) {
                    $availability = TicketAvailability::with('ticketType')->find($item->ticket_availability_id);
                    $orderTotalAmount += ((float) ($availability->ticketType->base_price ?? 0)) * (int) $item->quantity;
                }

                $order = Order::create([
                    'order_code'   => (string) Str::uuid(),
                    'user_id'      => $userId,
                    'guest_id'     => $guestId,
                    'order_date'   => now(),
                    'expired_at'   => now()->addMinutes(30),
                    'total_amount' => $orderTotalAmount,
                ]);

                Payment::create([
                    'order_id'       => $order->order_id,
                    'payment_method' => 'Credit Card',
                    'amount'         => $orderTotalAmount,
                    'payment_status' => 'Pending',
                ]);

                // STRICT: DO NOT create tickets here
                // STRICT: DO NOT delete cart here

                return $order;
            });
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }

        return redirect()->route('checkout.payments', $order->order_id);
    }

    public function paymentPage(Order $order): View
    {
        // Ownership validation
        $userId = Auth::id();
        $guestId = session('guest_id');

        $isOwner = ($order->user_id && $order->user_id == $userId) || 
                   ($order->guest_id && $order->guest_id == $guestId);

        if (!$isOwner) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load([
            'payment',
            'tickets.ticketAvailability.visitSchedule.location',
            'tickets.ticketAvailability.ticketType',
            'user',
            'guest',
        ]);

        if ($order->payment && $order->payment->payment_status === 'Paid') {
            return redirect()->route('order.show.detail', $order->order_id)
                ->with('info', 'This order has already been paid.');
        }

        return view('ordinary.checkout.payments.payments', [
            'order' => $order,
            'title' => 'Payment Confirmation',
        ]);
    }

    public function pay(Request $request, Order $order)
    {
        // Ownership validation
        $userId = Auth::id();
        $guestId = session('guest_id');
        $isOwner = ($order->user_id && $order->user_id == $userId) || 
                   ($order->guest_id && $order->guest_id == $guestId);

        if (!$isOwner) abort(403);

        if ($order->payment && $order->payment->payment_status === 'Paid') {
            return redirect()->route('ticket.checkout.success', $order->order_id)
                ->with('info', 'This order has already been paid.');
        }

        $billing = null;
        if (!auth()->check()) {
            $request->validate([
                'first_name'  => 'required',
                'last_name'   => 'required',
                'address'     => 'required',
                'city'        => 'required',
                'state'       => 'required',
                'postal_code' => 'required',
                'country'     => 'required',
            ]);

            $billing = [
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'address'     => $request->address,
                'city'        => $request->city,
                'state'       => $request->state,
                'postal_code' => $request->postal_code,
                'country'     => $request->country,
            ];
        }

        try {
            DB::transaction(function () use ($order, $userId, $guestId) {
                $payment = Payment::where('order_id', $order->order_id)
                    ->lockForUpdate()
                    ->first();

                if (!$payment || $payment->payment_status !== 'Pending') {
                    return;
                }

                $payment->update([
                    'payment_status' => 'Paid',
                    'paid_at'        => now(),
                ]);

                // Idempotency: DO NOT create again if tickets already exist
                if ($order->tickets()->exists()) {
                    return;
                }

                // Load Cart
                // Cart must act as temporary snapshot.
                $cartQuery = Cart::with('cartGroups.cartItems');
                if ($userId) {
                    $cart = $cartQuery->where('user_id', $userId)->first();
                } elseif ($guestId) {
                    $cart = $cartQuery->where('guest_id', $guestId)->first();
                } else {
                    $cart = $cartQuery->whereKey(session('cart_id'))->first();
                }

                if (!$cart) {
                    throw new \Exception('Cart not found. Cannot generate tickets.');
                }

                $cartItems = $cart->cartGroups->flatMap->cartItems;

                // Generate Tickets
                foreach ($cartItems as $item) {
                    for ($i = 0; $i < $item->quantity; $i++) {
                        Ticket::create([
                            'order_id'               => $order->order_id,
                            'ticket_availability_id' => $item->ticket_availability_id,
                            'qr_code'                => (string) Str::uuid(),
                            'status'                 => 'valid',
                        ]);
                    }
                }

                // Delete cart AFTER ticket creation
                $cart->cartGroups()->delete();
                $cart->delete();
            });
        } catch (\Exception $e) {
            Log::error('Payment settlement failed', ['order_id' => $order->order_id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Payment failed to process: ' . $e->getMessage());
        }

        // STEP 1: Load Order Relations
        $order->load([
            'tickets.ticketAvailability.ticketType',
            'guest'
        ]);

        // STEP 2: Determine Email Target
        $email = auth()->user()->email ?? optional($order->guest)->email;

        if ($email) {
            // STEP 3: Send Email Fail-Safe
            try {
                Mail::to($email)->send(new OrderSuccessMail($order, $billing));
            } catch (\Exception $e) {
                Log::error('Email sending failed', [
                    'order_id' => $order->order_id,
                    'error'    => $e->getMessage()
                ]);
            }
        } else {
            Log::error('Email sending failed: No email found for order', [
                'order_id' => $order->order_id
            ]);
        }

        return redirect()->route('ticket.checkout.success', $order->order_id)
            ->with('success', 'Payment successful! Your tickets have been generated.');
    }

    public function success(Order $order): View
    {
        $order->load([
            'payment',
            'tickets.ticketAvailability.visitSchedule.location',
            'tickets.ticketAvailability.ticketType',
            'user',
            'guest',
        ]);

        return view('ordinary.checkout.payments.success', [
            'order' => $order,
            'title' => 'Booking Confirmed',
        ]);
    }

    private function resolveCartContext(Request $request): array
    {
        $userId  = Auth::id();
        $guestId = $userId ? null : ($request->integer('guest_id') ?: session('guest_id'));

        $cartQuery = Cart::query()->with([
            'cartGroups.cartItems.ticketAvailability.ticketType',
            'cartGroups.cartItems.ticketAvailability.visitSchedule.location',
        ]);

        if ($userId) {
            $cart = $cartQuery->where('user_id', $userId)->where('expires_at', '>', now())->first();
        } elseif ($guestId) {
            $cart = $cartQuery->where('guest_id', $guestId)->where('expires_at', '>', now())->first();
        } elseif (session()->has('cart_id')) {
            $cart = $cartQuery->whereKey(session('cart_id'))->where('expires_at', '>', now())->first();
        } else {
            $cart = null;
        }

        return [
            'userId'  => $userId,
            'guestId' => $guestId,
            'cart'    => $cart,
        ];
    }

    private function resolveCustomerDefaults(?int $userId, ?int $guestId): array
    {
        $name  = '';
        $email = '';
        $user  = Auth::user();

        if ($userId && $user) {
            $email   = (string) $user->email;
            $profile = $user->profile;
            if ($profile) {
                $name = trim($profile->first_name . ' ' . $profile->last_name);
            }
        }

        if ($guestId) {
            $guest = Guest::find($guestId);

            if ($guest) {
                $name  = trim($guest->first_name . ' ' . $guest->last_name);
                $email = $guest->email;
            }
        }

        return [
            'name'  => $name,
            'email' => $email,
        ];
    }

    private function flattenCartItems(?Cart $cart): Collection
    {
        if (! $cart) {
            return collect();
        }

        return $cart->cartGroups
            ->flatMap(fn($group) => $group->cartItems)
            ->values();
    }

    private function resolveCheckoutErrorMessage(\Throwable $e): string
    {
        $knownMessages = [
            'Cart is empty.',
            'Guest session not found.',
            'Invalid ticket availability found in cart.',
            'Visit schedule not found for one or more cart items.',
            'Overbooking detected for selected visit schedule.',
        ];

        if (in_array($e->getMessage(), $knownMessages, true)) {
            return $e->getMessage();
        }

        return 'We could not complete checkout right now. Please try again.';
    }
}
