<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartGroup;
use App\Models\CartItem;
use App\Models\TicketAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function add(Request $request): JsonResponse | RedirectResponse
    {
        $validated = $request->validate([
            'ticket_availability_id' => ['required', 'integer', 'exists:ticket_availability,ticket_availability_id'],
            'quantity'               => ['required', 'integer', 'min:1'],
            'guest_id'               => ['nullable', 'integer', 'exists:guests,guest_id'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : session('guest_id');

        // AUTO-CREATE GUEST IF MISSING (ONLY ONCE)
        if (! $userId && ! $guestId) {
            $guest = \App\Models\Guest::create([
                'session_token' => session()->getId(),
            ]);
            $guestId = $guest->guest_id;
            session(['guest_id' => $guestId]);
        }

        $cart = $this->resolveCart($userId, $guestId);

        if (! $cart) {
            $cart = Cart::create([
                'user_id'    => $userId,
                'guest_id'   => $guestId,
                'expires_at' => now()->addHours(2),
            ]);

            session()->put('cart_id', $cart->cart_id);
        }

        if ($cart->expires_at && $cart->expires_at->isPast()) {
            $cart->update(['expires_at' => now()->addHours(2)]);
        }

        $cartGroup = $cart->cartGroups()->first();
        if (! $cartGroup) {
            $cartGroup = CartGroup::create([
                'cart_id' => $cart->cart_id,
            ]);
        }

        // IMMUTABLE RULE: No direct update, just add if exists
        CartItem::create([
            'cart_group_id'          => $cartGroup->cart_group_id,
            'ticket_availability_id' => $validated['ticket_availability_id'],
            'quantity'               => $validated['quantity'],
        ]);

        $cart->load([
            'cartGroups.cartItems.ticketAvailability.ticketType',
            'cartGroups.cartItems.ticketAvailability.visitSchedule.location',
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
            'guest_id' => ['nullable', 'integer', 'exists:guests,guest_id'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : ($validated['guest_id'] ?? session('guest_id'));

        $cart = $this->resolveCart($userId, $guestId);

        if ($cart) {
            $cart->load([
                'cartGroups.cartItems.ticketAvailability.ticketType',
                'cartGroups.cartItems.ticketAvailability.visitSchedule.location',
            ]);
        }

        $cartGroupsData = collect();
        $globalSubtotal  = 0;

        if ($cart) {
            foreach ($cart->cartGroups as $group) {
                $groupTotal = 0;
                $groupItems = collect();
                $locationName = 'Unknown Location';
                $visitDate = 'Unknown Date';

                foreach ($group->cartItems as $item) {
                    $ticketType  = $item->ticketAvailability?->ticketType;
                    $schedule    = $item->ticketAvailability?->visitSchedule;
                    $location    = $schedule?->location;

                    if ($location && $locationName === 'Unknown Location') {
                        $locationName = $location->name;
                    }
                    if ($schedule && $visitDate === 'Unknown Date') {
                        $rawDate = $schedule->visit_date;
                        $visitDate = $rawDate instanceof \Carbon\Carbon 
                            ? $rawDate->format('l, F j, Y') 
                            : \Carbon\Carbon::parse($rawDate)->format('l, F j, Y');
                    }

                    $price       = (float) ($ticketType?->base_price ?? 0);
                    $itemTotal   = $price * $item->quantity;
                    $groupTotal += $itemTotal;

                    $groupItems->push([
                        'id'                     => $item->cart_item_id,
                        'ticket_type'            => $ticketType?->name,
                        'quantity'               => $item->quantity,
                        'price'                  => $price,
                        'item_total'             => $itemTotal,
                        'ticket_availability_id' => $item->ticket_availability_id,
                    ]);
                }

                $globalSubtotal += $groupTotal;

                if ($groupItems->isNotEmpty()) {
                    $cartGroupsData->push([
                        'group_id'    => $group->cart_group_id,
                        'location'    => $locationName,
                        'visit_date'  => $visitDate,
                        'items'       => $groupItems,
                        'group_total' => $groupTotal,
                    ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'cart_groups'     => $cartGroupsData,
                    'global_subtotal' => $globalSubtotal,
                ],
            ]);
        }

        return view('ordinary.checkout.cart.cart', [
            'cartGroups'     => $cartGroupsData,
            'globalSubtotal' => $globalSubtotal,
            'title'          => 'Cart',
        ]);
    }

    private function resolveCart(?int $userId, ?int $guestId): ?Cart
    {
        $query = Cart::query()->where('expires_at', '>', now());

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

    public function storeAdmission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location_id'       => ['required', 'integer'],
            'visit_schedule_id' => ['required', 'integer'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.ticket_type_id' => ['required', 'integer'],
            'items.*.quantity'  => ['required', 'integer', 'min:1'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : session('guest_id');

        // AUTO-CREATE GUEST IF MISSING
        if (! $userId && ! $guestId) {
            $guest = \App\Models\Guest::create(['session_token' => session()->getId()]);
            $guestId = $guest->guest_id;
            session(['guest_id' => $guestId]);
        }

        try {
            DB::beginTransaction();

            $cart = $this->resolveCart($userId, $guestId);
            if (! $cart) {
                $cart = Cart::create([
                    'user_id'    => $userId,
                    'guest_id'   => $guestId,
                    'expires_at' => now()->addHours(2),
                ]);
                session()->put('cart_id', $cart->cart_id);
            }

            if ($cart->expires_at && $cart->expires_at->isPast()) {
                throw new \RuntimeException('Cart expired');
            }

            $cartGroup = CartGroup::create([
                'cart_id' => $cart->cart_id,
            ]);

            foreach ($validated['items'] as $item) {
                $availability = TicketAvailability::where('ticket_type_id', $item['ticket_type_id'])
                    ->where('visit_schedule_id', $validated['visit_schedule_id'])
                    ->first();

                if (! $availability) {
                    throw new \RuntimeException('Ticket availability not found for type ' . $item['ticket_type_id'] . ' on the selected schedule.');
                }

                CartItem::create([
                    'cart_group_id'          => $cartGroup->cart_group_id,
                    'ticket_availability_id' => $availability->ticket_availability_id,
                    'quantity'               => $item['quantity'],
                ]);
            }

            if (session()->has('modify_cart_group_id')) {
                $oldGroupId = session('modify_cart_group_id');
                
                $oldGroup = CartGroup::where('cart_group_id', $oldGroupId)
                    ->where('cart_id', $cart->cart_id)
                    ->lockForUpdate()
                    ->first();

                if (! $oldGroup) {
                    throw new \RuntimeException('Invalid modify target');
                }

                $oldGroup->cartItems()->delete();
                $oldGroup->delete();

                session()->forget('modify_cart_group_id');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'cart_group_id' => $cartGroup->cart_group_id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to store admission tickets. ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeGroup(Request $request, $id): JsonResponse
    {
        $userId  = Auth::id();
        $guestId = $userId ? null : session('guest_id');

        if (! $userId && ! $guestId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $cartGroup = CartGroup::where('cart_group_id', $id)
            ->whereHas('cart', function ($query) use ($userId, $guestId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_id', $guestId);
                }
            })
            ->first();

        if (! $cartGroup) {
            return response()->json(['success' => false, 'message' => 'Cart group not found or unauthorized.'], 404);
        }

        try {
            DB::beginTransaction();
            $cartGroup->cartItems()->delete();
            $cartGroup->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Group removed.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to remove group. ' . $e->getMessage()], 500);
        }
    }

    public function modifyGroup(Request $request, $id): RedirectResponse
    {
        $userId  = Auth::id();
        $guestId = $userId ? null : session('guest_id');

        $cartGroup = CartGroup::where('cart_group_id', $id)
            ->whereHas('cart', function ($query) use ($userId, $guestId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('guest_id', $guestId);
                }
            })->first();

        if (! $cartGroup) {
            abort(403, 'Unauthorized access to cart group.');
        }

        session(['modify_cart_group_id' => $id]);

        return redirect()->route('ticket.admission');
    }

    public function cancelModify(): RedirectResponse
    {
        session()->forget('modify_cart_group_id');
        return redirect()->route('ticket.cart');
    }
}
