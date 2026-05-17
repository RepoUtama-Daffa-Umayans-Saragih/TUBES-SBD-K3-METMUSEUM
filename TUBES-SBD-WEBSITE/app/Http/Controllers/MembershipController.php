<?php
namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
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
                'name'     => 'MET Membership',
                'price'    => 99,
                'duration' => '/month',
                'featured' => true,
                'features' => [
                    'Unlimited admission',
                    'Activation after email or gift claim',
                    'One membership plan for all members',
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
            'gift_email'         => ['nullable', 'email', 'max:255', 'required_if:is_gift,1'],
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

        $isGift         = (bool) ($validated['is_gift'] ?? false);
        $isAutoRenewal  = (bool) ($validated['auto_renewal'] ?? false);
        $recipientEmail = $isGift
            ? strtolower(trim((string) $validated['gift_email']))
            : strtolower(trim((string) (Auth::user()?->email ?? $validated['email'])));
        $membershipPrice = 99.00;
        $discountAmount  = $isAutoRenewal ? round($membershipPrice * 0.10, 2) : 0.00;
        $totalAmount     = round($membershipPrice - $discountAmount, 2);

        $order = DB::transaction(function () use ($request, $userId, $guestId, $isGift, $recipientEmail, $isAutoRenewal, $totalAmount) {
            $order = Order::create([
                'order_code'   => (string) Str::uuid(),
                'user_id'      => $userId,
                'guest_id'     => $guestId,
                'order_date'   => now(),
                'expired_at'   => now()->addMinutes(20),
                'total_amount' => $totalAmount,
                'order_type'   => 'membership',
            ]);

            Payment::create([
                'order_id'       => $order->order_id,
                'payment_method' => 'Membership',
                'amount'         => $totalAmount,
                'payment_status' => 'Pending',
            ]);

            $request->session()->put("membership_checkout_meta.{$order->order_id}", [
                'is_gift'         => $isGift,
                'auto_renewal'    => $isAutoRenewal,
                'recipient_email' => $recipientEmail,
            ]);

            return $order;
        });

        return redirect()->route('checkout.payments', $order->order_id);
    }

    public function activate(string $token): RedirectResponse
    {
        $membership = Membership::where('activation_token', $token)->firstOrFail();

        if ($membership->is_gift) {
            abort(404);
        }

        if ($membership->token_expires_at && $membership->token_expires_at->isPast()) {
            abort(403, 'Activation token has expired.');
        }

        if (! Auth::check()) {
            return redirect()->route('account.login')->with('info', 'Please log in to activate your membership.');
        }

        $user = Auth::user();

        if (strtolower((string) $user->email) !== strtolower((string) $membership->recipient_email)) {
            abort(403, 'Unauthorized.');
        }

        app(MembershipService::class)->activateMembership($membership, $user);

        return redirect()->route('account.index')->with('success', 'Membership activated successfully.');
    }

    public function claimGift(string $token): RedirectResponse
    {
        $membership = Membership::where('activation_token', $token)->firstOrFail();

        if (! $membership->is_gift) {
            abort(404);
        }

        if ($membership->token_expires_at && $membership->token_expires_at->isPast()) {
            abort(403, 'Gift claim token has expired.');
        }

        if (! Auth::check()) {
            return redirect()->route('account.login')->with('info', 'Please log in to claim your gift membership.');
        }

        $user = Auth::user();

        if (strtolower((string) $user->email) !== strtolower((string) $membership->recipient_email)) {
            abort(403, 'Unauthorized.');
        }

        /** @var MembershipService $membershipService */
        $membershipService = app(MembershipService::class);
        $result            = $membershipService->claimGiftMembership($membership, $user);

        return redirect()->route('account.index')->with('success', $result['message']);
    }
}
