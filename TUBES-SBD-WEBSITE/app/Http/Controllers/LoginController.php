<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display login form.
     */
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }

        return view('ordinary.account.login.login', [
            'title' => 'Login',
        ]);
    }

    /**
     * Handle login form submission.
     */
    public function login(Request $request)
    {
        $validated = $request->validate(
            [
                'email'    => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required'    => 'Email is required',
                'email.email'       => 'Please enter a valid email address',
                'password.required' => 'Password is required',
            ]
        );

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Account not found'])
                ->with('account_not_found', true)
                ->withInput($request->only('email'));
        }

        if (! Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            return back()
                ->withErrors(['password' => 'Incorrect password'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('account.index'))
            ->with('success', 'Welcome back! You have logged in successfully.');
    }
}
