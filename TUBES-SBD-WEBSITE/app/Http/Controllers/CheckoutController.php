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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Request $request): View | RedirectResponse | JsonResponse
    {
        $context = $this->resolveCartContext($request);

        if (! $context['cart'] || $context['cart']->cartItems->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cart is empty.'], 422);
            }

            return redirect()->route('ticket.cart')->with('error', 'Your cart is empty.');
        }

        $customer = $this->resolveCustomerDefaults($context['userId'], $context['guestId']);

        return view('ordinary.checkout.form', [
            'cart'      => $context['cart'],
            'cartItems' => $context['cart']->cartItems,
            'customer'  => $customer,
            'title'     => 'Checkout',
        ]);
    }

    public function checkout(Request $request): JsonResponse | RedirectResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $context = $this->resolveCartContext($request);
        $userId  = $context['userId'];
        $guestId = $context['guestId'];
        $cart    = $context['cart'];

        if (! $cart || $cart->cartItems->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cart is empty.',
                ], 422);
            }

            return redirect()->route('ticket.cart')->with('error', 'Cart is empty.');
        }

        try {
            $result = DB::transaction(function () use ($userId, $guestId, $validated) {
                $cart = Cart::query()
                    ->when($userId, fn($q) => $q->where('user_id', $userId))
                    ->when(! $userId && $guestId, fn($q) => $q->where('guest_id', $guestId))
                    ->when(! $userId && ! $guestId, fn($q) => $q->whereKey(session('cart_id')))
                    ->lockForUpdate()
                    ->with([
                        'cartItems.ticketAvailability.ticketType',
                        'cartItems.ticketAvailability.visitSchedule.location',
                    ])
                    ->first();

                if (! $cart || $cart->cartItems->isEmpty()) {
                    return null;
                }

                $checkoutGuest = null;

                if (! $userId) {
                    $nameParts = preg_split('/\s+/', trim($validated['name'])) ?: [];
                    $firstName = array_shift($nameParts) ?: $validated['name'];
                    $lastName  = trim(implode(' ', $nameParts));

                    if ($guestId) {
                        $checkoutGuest = Guest::query()->lockForUpdate()->find($guestId);

                        if (! $checkoutGuest) {
                            throw new \RuntimeException('Guest session not found.');
                        }

                        $checkoutGuest->update([
                            'email'         => $validated['email'],
                            'first_name'    => $firstName,
                            'last_name'     => $lastName !== '' ? $lastName : $firstName,
                            'session_token' => session()->getId(),
                        ]);
                    } else {
                        $checkoutGuest = Guest::create([
                            'email'         => $validated['email'],
                            'first_name'    => $firstName,
                            'last_name'     => $lastName !== '' ? $lastName : $firstName,
                            'session_token' => session()->getId(),
                        ]);

                        $cart->update([
                            'guest_id' => $checkoutGuest->id,
                        ]);
                    }
                }

                $availabilityIds = $cart->cartItems
                    ->pluck('ticket_availability_id')
                    ->unique()
                    ->values();

                $availabilities = TicketAvailability::with('visitSchedule')
                    ->whereIn('id', $availabilityIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $scheduleIds = $availabilities
                    ->pluck('visit_schedule_id')
                    ->filter()
                    ->unique()
                    ->values();

                $visitSchedules = VisitSchedule::whereIn('id', $scheduleIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $requestedPerSchedule = [];

                foreach ($cart->cartItems as $item) {
                    $availability = $availabilities->get($item->ticket_availability_id);

                    if (! $availability || ! $availability->visit_schedule_id) {
                        throw new \RuntimeException('Invalid ticket availability found in cart.');
                    }

                    $scheduleId                        = $availability->visit_schedule_id;
                    $requestedPerSchedule[$scheduleId] = ($requestedPerSchedule[$scheduleId] ?? 0) + $item->quantity;
                }

                foreach ($requestedPerSchedule as $scheduleId => $requestedQty) {
                    $schedule = $visitSchedules->get($scheduleId);

                    if (! $schedule) {
                        throw new \RuntimeException('Visit schedule not found for one or more cart items.');
                    }

                    $existingTicketsCount = Ticket::whereHas('ticketAvailability', function ($query) use ($scheduleId) {
                        $query->where('visit_schedule_id', $scheduleId);
                    })->lockForUpdate()->count();

                    if (($existingTicketsCount + $requestedQty) > $schedule->capacity_limit) {
                        throw new \RuntimeException('Overbooking detected for selected visit schedule.');
                    }
                }

                $order = Order::create([
                    'order_code'     => (string) Str::uuid(),
                    'user_id'        => $userId,
                    'guest_id'       => $guestId,
                    'order_date'     => now(),
                    'payment_status' => 'pending',
                    'expired_at'     => now()->addMinutes(30),
                ]);

                $createdTickets = collect();

                foreach ($cart->cartItems as $item) {
                    for ($i = 0; $i < $item->quantity; $i++) {
                        $ticket = Ticket::create([
                            'order_id'               => $order->id,
                            'ticket_availability_id' => $item->ticket_availability_id,
                            'qr_code'                => $this->generateUniqueQrCode(),
                            'status'                 => 'valid',
                            'used_at'                => null,
                        ]);

                        $createdTickets->push($ticket);
                    }
                }

                $payment = Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => 'simulation',
                    'payment_status' => 'paid',
                    'paid_at'        => now(),
                ]);

                $order->update([
                    'payment_status' => 'paid',
                ]);

                $cart->cartItems()->delete();

                $order->load([
                    'tickets.ticketAvailability.ticketType',
                    'tickets.ticketAvailability.visitSchedule.location',
                    'payment',
                ]);

                return [
                    'order'   => $order,
                    'tickets' => $createdTickets,
                    'payment' => $payment,
                ];
            });
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('ticket.checkout')->with('error', $e->getMessage());
        }

        if (! $result) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cart is empty.',
                ], 422);
            }

            return redirect()->route('ticket.cart')->with('error', 'Cart is empty.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Checkout completed successfully.',
                'data'    => $result,
            ]);
        }

        session()->forget('cart_id');

        return redirect()->route('ticket.checkout.success', $result['order'])
            ->with('success', 'Checkout completed successfully.');
    }

    private function generateUniqueQrCode(): string
    {
        do {
            $qrCode = (string) Str::uuid();
        } while (Ticket::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    public function success(Order $order): View
    {
        $order->load([
            'tickets.ticketAvailability.ticketType',
            'tickets.ticketAvailability.visitSchedule.location',
            'payment',
            'user',
            'guest',
        ]);

        return view('ordinary.checkout.success', [
            'order' => $order,
            'title' => 'Booking Success',
        ]);
    }

    private function resolveCartContext(Request $request): array
    {
        $userId  = Auth::id();
        $guestId = $userId ? null : ($request->integer('guest_id') ?: session('guest_id'));

        $cartQuery = Cart::query()->with([
            'cartItems.ticketAvailability.ticketType',
            'cartItems.ticketAvailability.visitSchedule.location',
        ]);

        if ($userId) {
            $cart = $cartQuery->where('user_id', $userId)->first();
        } elseif ($guestId) {
            $cart = $cartQuery->where('guest_id', $guestId)->first();
        } elseif (session()->has('cart_id')) {
            $cart = $cartQuery->whereKey(session('cart_id'))->first();
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

        if ($userId && Auth::user()) {
            $email = Auth::user()->email;
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
}
