@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/account/account/account.css')
@endpush

@section('title', 'My Account - MET Museum')

@section('content')
<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>My Account</h1>
            <p class="auth-subtitle">Manage your museum account</p>

            <div class="form-group">
                <label>Name</label>
                <div class="form-control" style="background:#f8f9fa;">{{ $user->profile?->first_name ?? '' }} {{ $user->profile?->last_name ?? '' }}</div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="form-control" style="background:#f8f9fa;">{{ $user->email }}</div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <div class="form-control" style="background:#f8f9fa;">{{ $user->profile?->address1 ?? '-' }}</div>
            </div>

            <form action="{{ route('account.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-block">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
