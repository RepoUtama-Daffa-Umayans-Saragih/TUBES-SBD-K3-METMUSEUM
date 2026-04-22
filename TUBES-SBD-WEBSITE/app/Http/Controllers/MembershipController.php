<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MembershipController extends Controller
{
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

        return view('ordinary.membership.membership', [
            'memberships' => $memberships,
            'title'       => 'Membership',
        ]);
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
