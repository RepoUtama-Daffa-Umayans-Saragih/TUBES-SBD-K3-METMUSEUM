<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountController extends Controller
{
    /**
     * Display check account form
     */
    public function show()
    {
        if (Auth::check() || session('guest_id')) {
            return redirect()->route('account.index');
        }

        return view('ordinary.account.account-check.account-check', [
            'title' => 'Check for an Account',
        ]);
    }

    /**
     * Handle check account submission
     */
    public function check(Request $request)
    {
        $validated = $request->validate([
            'email'      => ['required', 'email'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
        ]);

        // Check if user exists with this email
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            // User exists - redirect to forgot password with email pre-filled
            return redirect()->route('account.forgot-password')
                ->with('email', $validated['email'])
                ->with('info', 'An account exists for this email. Please reset your password.');
        }

        // User does not exist - redirect to register with email pre-filled
        return redirect()->route('account.register')
            ->with('email', $validated['email'])
            ->with('first_name', $validated['first_name'])
            ->with('last_name', $validated['last_name'])
            ->with('info', 'No account found. Please create one below.');
    }
}
