<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display register form
     */
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }

        return view('ordinary.account.register.register', [
            'title' => 'Register',
        ]);
    }

    /**
     * Display user account/dashboard
     */
    public function account()
    {
        if (! Auth::check()) {
            return redirect()->route('account.login');
        }

        return view('ordinary.account.account.account', [
            'user' => Auth::user(),
            'title' => 'My Account',
        ]);
    }

    /**
     * Display login form
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('account.index');
        }

        return view('ordinary.account.login.login', [
            'title' => 'Login',
        ]);
    }

    /**
     * Handle login form submission
     */
    public function handleLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('account.index'))->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout for both user and guest
     */
    public function logout(Request $request)
    {
        // Handle user logout
        if (Auth::check()) {
            Auth::logout();
        }

        // Handle guest logout
        $request->session()->forget('guest_user');
        $request->session()->forget('guest_id');
        $request->session()->forget('guest_name');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }

    /**
     * Display forgot password form
     */
    public function forgotPassword()
    {
        if (Auth::check() || session('guest_id')) {
            return redirect()->route('account.index');
        }

        return view('ordinary.account.forgot-password.forgot-password', [
            'title' => 'Forgot Password',
            'email' => session('email'),
        ]);
    }

    /**
     * Handle forgot password submission
     */
    public function handleForgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user exists
        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return redirect()->route('account.register')
                ->with('email', $validated['email'])
                ->with('info', 'No account found with this email. Please create one.');
        }

        // TODO: Send password reset email
        // For now, just show a message

        return back()->with('status', 'If that email address is in our system, we have sent password reset instructions.');
    }
}
