<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function add(Request $request): JsonResponse | RedirectResponse
    {
        $validated = $request->validate([
            'ticket_availability_id' => ['required', 'integer', 'exists:ticket_availability,id'],
            'quantity'               => ['required', 'integer', 'min:1'],
            'guest_id'               => ['nullable', 'integer', 'exists:guests,id'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : ($validated['guest_id'] ?? session('guest_id'));

        if (! $userId && ! $guestId && ! session()->has('cart_id')) {
            $cart = Cart::create([
                'user_id'  => null,
                'guest_id' => null,
            ]);

            session()->put('cart_id', $cart->id);
        }

        $cart = $this->resolveCart($userId, $guestId);

        if (! $cart) {
            $cart = Cart::create([
                'user_id'  => $userId,
                'guest_id' => $guestId,
            ]);

            if (! $userId && ! $guestId) {
                session()->put('cart_id', $cart->id);
            }
        }

        $cartItem = $cart->cartItems()
            ->where('ticket_availability_id', $validated['ticket_availability_id'])
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity'],
            ]);
        } else {
            CartItem::create([
                'cart_id'                => $cart->id,
                'ticket_availability_id' => $validated['ticket_availability_id'],
                'quantity'               => $validated['quantity'],
            ]);
        }

        $cart->load([
            'cartItems.ticketAvailability.ticketType',
            'cartItems.ticketAvailability.visitSchedule',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Item added to cart.',
                'data'    => $cart,
            ], 201);
        }

        return redirect()->route('ticket.cart')->with('success', 'Item added to cart.');
    }

    public function index(Request $request): View | JsonResponse
    {
        $validated = $request->validate([
            'guest_id' => ['nullable', 'integer', 'exists:guests,id'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : ($validated['guest_id'] ?? session('guest_id'));

        $cart = $this->resolveCart($userId, $guestId);

        if ($cart) {
            $cart->load([
                'cartItems.ticketAvailability.ticketType',
                'cartItems.ticketAvailability.visitSchedule.location',
            ]);
        }

        $cartItems = collect();
        $subtotal  = 0;

        if ($cart) {
            $cartItems = $cart->cartItems->map(function (CartItem $item) use (&$subtotal) {
                $ticketType  = $item->ticketAvailability?->ticketType;
                $price       = (float) ($ticketType?->base_price ?? 0);
                $total       = $price * $item->quantity;
                $subtotal   += $total;

                return [
                    'id'                     => $item->id,
                    'ticket_type'            => $ticketType?->name,
                    'schedule'               => optional($item->ticketAvailability?->visitSchedule?->visit_date)->format('Y-m-d'),
                    'location'               => $item->ticketAvailability?->visitSchedule?->location?->name,
                    'quantity'               => $item->quantity,
                    'price'                  => $price,
                    'total'                  => $total,
                    'ticket_availability_id' => $item->ticket_availability_id,
                ];
            });
        }

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'cart'       => $cart,
                    'cart_items' => $cartItems,
                    'subtotal'   => $subtotal,
                ],
            ]);
        }

        return view('ordinary.checkout.cart.cart', [
            'cart'      => $cart,
            'cartItems' => $cartItems,
            'subtotal'  => $subtotal,
            'title'     => 'Cart',
        ]);
    }

    private function resolveCart(?int $userId, ?int $guestId): ?Cart
    {
        $query = Cart::query();

        if ($userId) {
            return $query->where('user_id', $userId)->first();
        }

        if ($guestId) {
            return $query->where('guest_id', $guestId)->first();
        }

        if (session()->has('cart_id')) {
            return $query->whereKey(session('cart_id'))->first();
        }

        return null;
    }
}
