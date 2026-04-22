@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/ticket/checkout/checkout.css')
@endpush

@section('title', 'Checkout - MET Museum')

@section('content')
<div class="checkout-wrapper">
    <a href="{{ route('ticket.cart') }}" class="back-link">← Back to Cart</a>

    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Complete your MET Museum ticket order</p>
    </div>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endforeach
    @endif

    <form action="{{ route('ticket.checkout.process') }}" method="POST" class="checkout-content">
        @csrf

        <div class="checkout-form">
            <div class="form-section-title">Visit Information</div>

            <div class="form-group">
                <label for="visit_date">Visit Date</label>
                <input
                    type="date"
                    id="visit_date"
                    name="visit_date"
                    value="{{ old('visit_date') }}"
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    required>
                <small style="color: #999; display: block; margin-top: 0.5rem;">Please select a date from tomorrow onwards</small>
                @error('visit_date')
                    <small style="color: #dc3545; display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="alert alert-info" style="margin-top: 2rem;">
                <strong>Visitor Information:</strong>
                This order will be registered for <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                <br><small>Your tickets will be saved to your account.</small>
            </div>

            <button type="submit" class="btn btn-primary btn-large">Complete Purchase</button>
        </div>

        <div class="checkout-summary">
            <div class="summary-title">Order Summary</div>

            <div class="summary-items">
                @php
                    $itemCount = 0;

                    foreach ($cartItems as $item) {
