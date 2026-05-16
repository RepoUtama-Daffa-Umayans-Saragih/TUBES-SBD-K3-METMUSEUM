<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Throwable;

class MembershipController extends Controller
{
    public function addMember()
    {
        $authUser = Auth::user();
        $profile  = $authUser?->profile;

        $user = (object) [
            'first_name' => $profile?->first_name ?? '',
            'last_name'  => $profile?->last_name ?? '',
            'email'      => $authUser?->email ?? '',
        ];

        return view('ordinary.member.add-member.add-member', [
            'user'  => $user,
            'title' => 'Membership Information',
        ]);
    }

    public function information()
    {
        return $this->addMember();
    }

    public function index()
    {
        $memberships = [
            [
                'id'       => 1,
                'name'     => 'Individual',
                'price'    => 99,
                'duration' => '/year',
                'featured' => false,
                'features' => [
                    'Unlimited admission',
                    'Member events',
                    '10% gift shop discount',
                    'Member magazine',
                ],
            ],
            [
                'id'       => 2,
                'name'     => 'Family',
                'price'    => 199,
                'duration' => '/year',
                'featured' => true,
                'features' => [
                    'Unlimited admission for 2 adults + 1 child',
                    'Member events',
                    '15% gift shop discount',
                    'Member magazine',
                    'Priority access to exhibitions',
                ],
            ],
            [
                'id'       => 3,
                'name'     => 'Patron',
                'price'    => 500,
                'duration' => '/year',
                'featured' => false,
                'features' => [
                    'Unlimited admission + up to 4 guests',
                    'VIP events access',
                    '20% gift shop discount',
                    'Member magazine',
                    'Priority access to exhibitions',
                    'Exclusive Patron benefits',
                ],
            ],
        ];

        $viewName = 'ordinary.member.membership.membership';

        if (! View::exists($viewName)) {
            return redirect('/member/add-member');
        }

        try {
            return view($viewName, [
                'memberships' => $memberships,
                'title'       => 'Membership',
            ]);
        } catch (Throwable $exception) {
            return redirect('/member/add-member');
        }
    }

    public function show($id)
    {
        $membership = null;

        return view('ordinary.membership.show.show', [
            'membership' => $membership,
            'title'      => 'Membership Details',
        ]);
    }

    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'membership_id'      => ['nullable', 'integer'],
            'is_gift'            => ['nullable', 'boolean'],
            'auto_renewal'       => ['nullable', 'boolean'],
            'first_name'         => ['required', 'string', 'max:100'],
            'last_name'          => ['required', 'string', 'max:100'],
            'email'              => ['required', 'email', 'max:255'],
            'gift_first_name'    => ['nullable', 'string', 'max:100'],
            'gift_last_name'     => ['nullable', 'string', 'max:100'],
            'gift_email'         => ['nullable', 'email', 'max:255'],
            'street_address'     => ['nullable', 'string', 'max:255'],
            'apartment'          => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:100'],
            'country'            => ['nullable', 'string', 'max:100'],
            'postal_code'        => ['nullable', 'string', 'max:30'],
            'ship_to'            => ['nullable', 'in:recipient,donor'],
            'email_confirmation' => ['nullable', 'in:both,donor'],
        ]);

        $userId  = Auth::id();
        $guestId = $userId ? null : session('guest_id');

        if (! $userId && ! $guestId) {
            abort(403, 'User or guest identity not found.');
        }

        $membershipId = (int) ($validated['membership_id'] ?? 1);
        $membershipCatalog = [
            1 => ['name' => 'Individual', 'price' => 99.00],
            2 => ['name' => 'Family', 'price' => 199.00],
            3 => ['name' => 'Patron', 'price' => 500.00],
        ];

        $membership = $membershipCatalog[$membershipId] ?? $membershipCatalog[1];

        $order = DB::transaction(function () use ($userId, $guestId, $membership) {
            $order = Order::create([
                'order_code'   => (string) Str::uuid(),
                'user_id'      => $userId,
                'guest_id'     => $guestId,
                'order_date'   => now(),
                'expired_at'   => now()->addMinutes(20),
                'total_amount' => $membership['price'],
            ]);

            Payment::create([
                'order_id'       => $order->order_id,
                'payment_method' => 'Membership',
                'amount'         => $membership['price'],
                'payment_status' => 'Pending',
            ]);

            return $order;
        });

        return redirect()->route('checkout.payments', $order->order_id);
    }
}
