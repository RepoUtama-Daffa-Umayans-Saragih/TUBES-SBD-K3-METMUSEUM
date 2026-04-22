@extends('layouts.app')

@section('content')
@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/checkout/cart/cart.css')
@endpush
<div class="booking-page">
    <div class="booking-header">
        <h1 class="booking-title">Cart</h1>
        <p class="booking-subtitle">Review your selected tickets before continuing to checkout.</p>
    </div>

    <div class="booking-stepper" aria-label="Booking steps">
        <div class="booking-step">Step 1: Select Date</div>
        <div class="booking-step">Step 2: Select Tickets</div>
        <div class="booking-step">Step 3: Add to Cart</div>
        <div class="booking-step is-active">Step 4: View Cart</div>
        <div class="booking-step">Step 5: Checkout</div>
        <div class="booking-step">Step 6: Confirmation</div>
    </div>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-banner error">{{ session('error') }}</div>
    @endif

    <div class="booking-grid two-col">
        <div class="booking-card">
            <div class="booking-card-title">Cart items</div>

            @forelse($cartItems as $item)
                <div class="summary-row">
                    <div class="summary-title-line">
                        <span>{{ $item['ticket_type'] }}</span>
                        <span>${{ number_format($item['total'], 2) }}</span>
                    </div>
                    <div class="summary-subline">{{ $item['location'] }} · {{ $item['schedule'] }}</div>
                    <div class="summary-subline">Quantity: {{ $item['quantity'] }} · Unit price: ${{ number_format($item['price'], 2) }}</div>
                </div>
            @empty
                <p>Your cart is empty.</p>
            @endforelse
        </div>

        <aside class="booking-card booking-summary">
            <div class="booking-card-title">Order summary</div>
            <div class="summary-total">
                <span>Total</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="booking-actions">
                <a href="{{ route('ticket.checkout') }}" class="booking-btn">Proceed to checkout</a>
                <a href="{{ route('ticket.index') }}" class="booking-btn-outline">Continue browsing</a>
            </div>
        </aside>
    </div>
</div>
@endsection@extends('layouts.app')

@section('content')
<div class="booking-page">
    <div class="booking-header">
        <h1 class="booking-title">Cart</h1>
        <p class="booking-subtitle">Review your selected tickets before continuing to checkout.</p>
    </div>

    <div class="booking-stepper" aria-label="Booking steps">
        <div class="booking-step">Step 1: Select Date</div>
        <div class="booking-step">Step 2: Select Tickets</div>
        <div class="booking-step">Step 3: Add to Cart</div>
        <div class="booking-step is-active">Step 4: View Cart</div>
        <div class="booking-step">Step 5: Checkout</div>
        <div class="booking-step">Step 6: Confirmation</div>
    </div>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-banner error">{{ session('error') }}</div>
    @endif

    <div class="booking-grid two-col">
        <div class="booking-card">
            <div class="booking-card-title">Cart items</div>

            @forelse($cartItems as $item)
                <div class="summary-row">
                    <div class="summary-title-line">
                        <span>{{ $item['ticket_type'] }}</span>
                        <span>${{ number_format($item['total'], 2) }}</span>
                    </div>
                    <div class="summary-subline">{{ $item['location'] }} · {{ $item['schedule'] }}</div>
                    <div class="summary-subline">Quantity: {{ $item['quantity'] }} · Unit price: ${{ number_format($item['price'], 2) }}</div>
                </div>
            @empty
                <p>Your cart is empty.</p>
            @endforelse
        </div>

        <aside class="booking-card booking-summary">
            <div class="booking-card-title">Order summary</div>
            <div class="summary-total">
                <span>Total</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="booking-actions">
                <a href="{{ route('ticket.checkout') }}" class="booking-btn">Proceed to checkout</a>
                <a href="{{ route('ticket.index') }}" class="booking-btn-outline">Continue browsing</a>
            </div>
        </aside>
    </div>
</div>
@endsection
