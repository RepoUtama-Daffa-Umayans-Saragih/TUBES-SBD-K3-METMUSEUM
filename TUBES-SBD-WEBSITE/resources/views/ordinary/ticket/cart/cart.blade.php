@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/ticket/cart/cart.css')
@endpush

@section('title', 'Shopping Cart - MET Museum')

@section('content')
<div class="cart-wrapper">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
    </div>

    <div class="cart-content">
        <div class="cart-items">
            @if(isset($cartItems) && count($cartItems) > 0)
                @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="item-info">
                            <h3>{{ $item['ticket']->category }}</h3>
                            <p class="item-location">{{ $item['ticket']->location->name }}</p>
                        </div>
                        <div class="item-price">${{ number_format($item['price'], 2) }}</div>
                        <div class="item-quantity">
                            <input type="number" class="item-qty-input" value="{{ $item['quantity'] }}" min="1" disabled>
                        </div>
                        <div class="item-total">${{ number_format($item['subtotal'], 2) }}</div>
                        <button class="item-remove-btn" data-ticket-id="{{ $item['ticket_id'] }}" onclick="removeFromCart(this)">Remove</button>
                    </div>
                @endforeach
            @else
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="{{ route('ticket.admission') }}" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            @endif
        </div>

        @if(isset($cartItems) && count($cartItems) > 0)
            <div class="cart-summary">
                <div class="summary-title">Order Summary</div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                @if($isMember && $memberDiscount > 0)
                    <div class="summary-row" style="color: #28a745; border-bottom: 1px solid #f0f0f0; padding: 0.75rem 0;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <strong>Member Discount (10%)</strong>
                            <span style="font-size: 0.75rem; background-color: #28a745; color: #fff; padding: 0.25rem 0.5rem; border-radius: 2px;">MEMBER</span>
                        </span>
                        <span>-${{ number_format($memberDiscount, 2) }}</span>
                    </div>
                @endif
                <div class="summary-row">
                    <span>Tax (10%)</span>
                    <span>${{ number_format($tax, 2) }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('ticket.checkout') }}" class="checkout-btn">Proceed to Checkout</a>
                <a href="{{ route('ticket.admission') }}" class="continue-shopping-btn">Continue Shopping</a>
            </div>
        @endif
    </div>
</div>

<script>
    function removeFromCart(button) {
        // Get ticket ID from data attribute
        const ticketId = button.getAttribute('data-ticket-id');
        // This will need to be implemented with a remove-from-cart route
        alert('Remove from cart functionality to be implemented for ticket ID: ' + ticketId);
    }
</script>
@endsection
