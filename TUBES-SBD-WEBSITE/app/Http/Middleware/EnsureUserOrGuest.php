<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOrGuest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() || $this->hasValidGuestSession($request)) {
            return $next($request);
        }

        $request->session()->forget(['guest_user', 'guest_id', 'guest_name']);

        return redirect()->route('login')
            ->with('error', 'Please login or continue as a guest to access admission.');
    }

    /**
     * Validate the expected guest session structure.
     */
    private function hasValidGuestSession(Request $request): bool
    {
        $guest = $request->session()->get('guest_user');

        if (! is_array($guest)) {
            return false;
        }

        if (! array_key_exists('id', $guest)) {
            return false;
        }

        $guestId = filter_var($guest['id'], FILTER_VALIDATE_INT);

        if ($guestId === false || $guestId < 1) {
            return false;
        }

        if (array_key_exists('name', $guest) && ! is_null($guest['name']) && ! is_string($guest['name'])) {
            return false;
        }

        return true;
    }
}
