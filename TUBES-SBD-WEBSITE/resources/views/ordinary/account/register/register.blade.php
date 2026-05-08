@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/account/register/register.css')
@endpush

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Create Account</h1>
            <p class="auth-subtitle">Join us to enhance your museum experience</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Check for Account Section -->
            <div class="check-account-box">
                <p class="check-account-text">
                    If you are a Member, or if your email may already be on record with the Museum, please click the button below to check if you already have an account and reset your password.
                </p>
                <a href="{{ route('account.account-check') }}" class="btn btn-primary btn-check-account">
                    Check for account
                </a>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="auth-form" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" maxlength="100" required>
                    @error('first_name')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" maxlength="100" required>
                    @error('last_name')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                    @error('phone_number')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address1">Address Line 1</label>
                    <input type="text" id="address1" name="address1" class="form-control @error('address1') is-invalid @enderror" value="{{ old('address1') }}" required>
                    @error('address1')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address2">Address Line 2 (Optional)</label>
                    <input type="text" id="address2" name="address2" class="form-control @error('address2') is-invalid @enderror" value="{{ old('address2') }}">
                    @error('address2')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                    @error('city')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" required>
                    @error('state')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}" required>
                    @error('country')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" required>
                    @error('postal_code')
                        <small class="field-error-message">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="{{ route('account.login') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
