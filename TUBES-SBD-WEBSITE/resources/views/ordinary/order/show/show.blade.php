@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/order/show/show.css')
@endpush

@section('title', 'Order Confirmation - MET Museum')

@section('content')
<div class="confirmation-container">
    @if(session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-left: 4px solid #28a745; padding: 1rem; margin-bottom: 2rem; color: #155724; border-radius: 2px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="confirmation-header">
        <div class="success-badge">✓</div>
        <h1>Order Confirmed</h1>
        <p>Thank you for your purchase! Your order is ready.</p>
    </div>

    <div class="alert-info">
        <strong>Order ID:</strong> {{ $order->id }}<br>
        <small>Please save this number for your records. You'll need it to access your tickets.</small>
    </div>

    <div class="order-card">
        <div class="order-section-title">Order Details</div>

        <div class="meta-info">
            <div class="meta-label">Order Date:</div>
            <div class="meta-value">{{ $order->order_date->format('M d, Y \\a\\t h:i A') }}</div>

            <div class="meta-label">Visit Date:</div>
            <div class="meta-value">{{ \Carbon\Carbon::parse($order->visit_date)->format('M d, Y') }}</div>

            <div class="meta-label">Status:</div>
            <div class="meta-value">
                <strong>{{ $order->payment_status }}</strong>
                @if($order->payment_status === 'Pending')
                    <spans style="color: #ff9800;"> - Payment awaiting</spans>
                @endif
            </div>
        </div>

        <div class="order-section-title">Tickets</div>

        @forelse($order->orderDetails as $detail)
            <div class="order-item">
                <div class="item-details">
                    <div class="item-name">{{ $detail->ticket->category }}</div>
                    <div class="item-meta">
                        Location: {{ $detail->ticket->location->name }}<br>
                        Quantity: {{ $detail->quantity }}
                    </div>
                </div>
                <div class="item-price">${{ number_format($detail->ticket->price * $detail->quantity, 2) }}</div>
            </div>
        @empty
            <p style="color: #999;">No ticket details available.</p>
        @endforelse

        <div class="total-row">
            <span>Total Amount:</span>
            <span>${{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="actions">
        <a href="/art/collection" class="button secondary">Continue Browsing</a>
        <a href="/tickets" class="button primary">Back to Tickets</a>
    </div>
</div>
@endsection
