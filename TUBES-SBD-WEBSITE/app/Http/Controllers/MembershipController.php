<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Throwable;

class MembershipController extends Controller
{
    /**
     * Display the membership add-member form.
     */
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

    /**
     * Backward-compatible alias for existing references.
     */
    public function information()
    {
        return $this->addMember();
    }

    /**
     * Display membership tiers and details
     */
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

        if (!View::exists($viewName)) {
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

    /**
     * Display membership details
     */
    public function show($id)
    {
                            // Get membership details
        $membership = null; // Replace with actual query

        return view('ordinary.membership.show.show', [
            'membership' => $membership,
            'title'      => 'Membership Details',
        ]);
    }

    /**
     * Handle membership purchase
     */
    public function purchase(Request $request)
    {
        $membershipId = $request->input('membership_id');

        // TODO: Process membership purchase

        return redirect('/')->with('success', 'Membership purchased successfully!');
    }
}
