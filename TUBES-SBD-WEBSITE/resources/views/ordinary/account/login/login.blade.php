@extends('ordinary.account.login.layout')

@push('styles')
@vite('resources/css/ordinary/account/login/login.css')
@endpush

@section('content')
<section class="login-shell" aria-labelledby="login-heading">
    <div class="login-panel login-panel-main">
        <h1 id="login-heading" class="login-title">Login</h1>

        <p class="login-lead">Log in or create an account to continue.</p>

        <p class="login-copy">
            If you are a Member, or if your email may already be on record with the Museum, please click the
            button below to check if you already have an account and reset your password.
        </p>

        <a href="{{ route('account.account-check') }}" class="btn btn-outline btn-check">Check for account</a>

        <div class="login-rule"></div>

        @if ($errors->any())
            <div class="login-error-box" role="alert">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                @if (session('account_not_found'))
                    <p class="login-help-text">
                        If you forgot your password click on
                        <a href="{{ route('account.forgot-password') }}">Forgot Password?</a>
                        below to reset it. To register click on
                        <a href="{{ route('account.register') }}">Create an Account</a>.
                    </p>
                @endif
            </div>
        @endif

        <form action="{{ route('account.login.submit') }}" method="POST" class="login-form">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-field">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" id="togglePassword" class="password-toggle" aria-label="Show password">
                        <span aria-hidden="true">o</span>
                    </button>
                </div>
            </div>

            <a href="{{ route('account.forgot-password') }}" class="forgot-link">Forgot password?</a>

            <p class="terms-copy">
                By logging in, you agree to our
                <a href="#">Terms of Service</a>
                and
                <a href="#">Privacy Policy</a>
            </p>

            <button type="submit" class="btn btn-primary btn-login">Log in</button>

            <a href="{{ route('account.register') }}" class="btn btn-outline btn-create">Create an account</a>
        </form>
    </div>

    <aside class="guest-panel" aria-labelledby="guest-heading">
        <h2 id="guest-heading" class="guest-title">Guest Checkout</h2>

        <p class="guest-lead">Enter your name and email here.</p>

        <div class="guest-error-summary" id="guest-error-summary" aria-live="polite">
            @if ($errors->guestCheckout->any())
                <ul>
                    @foreach ($errors->guestCheckout->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <form action="{{ route('guest.login') }}" method="POST" class="guest-form" novalidate data-guest-checkout-form>
            @csrf

            <div class="field">
                <label for="guest-email">Email</label>
                <div class="guest-input-wrap">
                    <input
                        type="email"
                        id="guest-email"
                        name="email"
                        value="{{ old('email') }}"
                        data-guest-field="email"
                        class="@error('email', 'guestCheckout') is-invalid @enderror"
                    >
                    <span class="field-status" aria-hidden="true">i</span>
                </div>
                <div class="field-error-message" id="guest-email-error" data-error-for="email">
                    @error('email', 'guestCheckout'){{ $message }}@enderror
                </div>
            </div>

            <div class="field">
                <label for="guest-confirm-email">Please confirm email address.</label>
                <div class="guest-input-wrap">
                    <input
                        type="email"
                        id="guest-confirm-email"
                        name="confirm_email"
                        value="{{ old('confirm_email') }}"
                        placeholder="Please confirm email address."
                        data-guest-field="confirm_email"
                        class="@error('confirm_email', 'guestCheckout') is-invalid @enderror"
                    >
                    <span class="field-status" aria-hidden="true">i</span>
                </div>
                <div class="field-error-message" id="guest-confirm-email-error" data-error-for="confirm_email">
                    @error('confirm_email', 'guestCheckout'){{ $message }}@enderror
                </div>
            </div>

            <div class="guest-name-row">
                <div class="field">
                    <label for="guest-first-name">First Name</label>
                    <div class="guest-input-wrap">
                        <input
                            type="text"
                            id="guest-first-name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            placeholder="First Name"
                            data-guest-field="first_name"
                            class="@error('first_name', 'guestCheckout') is-invalid @enderror"
                        >
                        <span class="field-status" aria-hidden="true">i</span>
                    </div>
                    <div class="field-error-message" id="guest-first-name-error" data-error-for="first_name">
                        @error('first_name', 'guestCheckout'){{ $message }}@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="guest-last-name">Last Name</label>
                    <div class="guest-input-wrap">
                        <input
                            type="text"
                            id="guest-last-name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            placeholder="Last Name"
                            data-guest-field="last_name"
                            class="@error('last_name', 'guestCheckout') is-invalid @enderror"
                        >
                        <span class="field-status" aria-hidden="true">i</span>
                    </div>
                    <div class="field-error-message" id="guest-last-name-error" data-error-for="last_name">
                        @error('last_name', 'guestCheckout'){{ $message }}@enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-guest" data-guest-submit>Continue as guest</button>
        </form>
    </aside>
</section>

@push('scripts')
<script src="{{ asset('js/guest-checkout.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');

        if (!input || !toggle) {
            return;
        }

        toggle.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    });
</script>
@endpush
@endsection
