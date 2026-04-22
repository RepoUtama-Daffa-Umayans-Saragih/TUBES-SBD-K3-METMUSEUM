<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestCheckoutRequest;
use App\Models\Guest;

class GuestCheckoutController extends Controller
{
    /**
     * Store guest checkout identity in database and session.
     */
    public function store(GuestCheckoutRequest $request)
    {
        $validated = $request->validated();
        $sessionToken = $request->session()->getId();

        $guest = Guest::updateOrCreate(
            ['session_token' => $sessionToken],
            [
                'email' => strtolower($validated['email']),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'session_token' => $sessionToken,
                'created_at' => now(),
            ]
        );

        session([
            'guest_user' => [
                'id' => $guest->id,
                'name' => trim($guest->first_name.' '.$guest->last_name),
            ],
            'guest_id' => $guest->id,
            'guest_name' => $guest->first_name,
        ]);

        return redirect()->intended(route('ticket.checkout'))
            ->with('success', 'Guest profile saved successfully.');
    }
}
