@extends('layouts.app')

@push('styles')
@vite('resources/css/ordinary/account/account-check/account-check.css')
@endpush

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Check for an Account</h1>
            <p class="auth-subtitle">Enter your information to find your account or register</p>

            @if(session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('account.account-check.submit') }}" method="POST" class="auth-form">
                @csrf

                <div class="form-row">
                    <div class="form-group half">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}">
                    </div>

                    <div class="form-group half">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <button type="submit" class="btn-primary">Find My Account</button>

                <a href="{{ route('account.login') }}" class="btn-secondary">
                    Back to Login
                </a>
            </form>

            <div class="auth-links">
                <p>Know your password? <a href="{{ route('account.login') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
