@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/order/show/show.css')
@endpush

@section('title', $mode === 'list' ? 'My Orders - MET Museum' : 'Order Details - MET Museum')

@section('content')
<div class="order-container">
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($mode === 'list')
        <div class="order-header">
            <h1>My Orders</h1>
            <p>View your past ticket purchases and payment status.</p>
        </div>

        <div class="order-list">
            @forelse($orders as $order)
                <a href="{{ route('order.show.detail', $order->order_id) }}" class="order-card {{ strtolower($order->payment?->payment_status ?? 'pending') }}">
                    <div class="card-header">
                        <span class="order-id">Order Code: {{ substr($order->order_code, 0, 8) }}...</span>
                        <span class="order-status {{ strtolower($order->payment?->payment_status ?? 'pending') }}">
                            {{ $order->payment?->payment_status ?? 'Pending' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="card-row">
                            <span class="label">Date:</span>
                            <span class="value">{{ optional($order->order_date)->format('M d, Y h:i A') ?? 'N/A' }}</span>
                        </div>
                        <div class="card-row">
                            <span class="label">Total Amount:</span>
                            <span class="value">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        View Details &rarr;
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <p>You have no orders yet.</p>
                    <a href="{{ route('ticket.admission') }}" class="btn-primary">Browse Tickets</a>
                </div>
            @endforelse
        </div>
    @elseif($mode === 'detail')
        <div class="order-header detail-header">
            <a href="{{ route('order.show') }}" class="back-link">&larr; Back to Orders</a>
            <h1>Order Details</h1>
            <p>Order Code: {{ $order->order_code }}</p>
        </div>

        <div class="detail-layout">
            <div class="detail-sidebar">
                <div class="summary-card">
                    <h3>Summary</h3>
                    <div class="summary-row">
                        <span class="label">Order Date:</span>
                        <span class="value">{{ optional($order->order_date)->format('M d, Y h:i A') ?? 'N/A' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Payment Status:</span>
                        <span class="value status-badge {{ strtolower($order->payment?->payment_status ?? 'pending') }}">
                            {{ $order->payment?->payment_status ?? 'Pending' }}
                        </span>
                    </div>
                    <div class="summary-row total">
                        <span class="label">Total:</span>
                        <span class="value">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    
                    @if(($order->payment?->payment_status ?? 'Pending') === 'Pending')
                        <form action="{{ route('ticket.checkout.pay', $order->order_id) }}" method="POST" style="margin-top: 15px;">
                            @csrf
                            <button type="submit" class="btn-pay">Simulate Payment (Dev)</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="detail-main">
                <h3>Tickets</h3>
                <div class="ticket-list">
                    @forelse($order->tickets as $ticket)
                        <div class="ticket-card">
                            <div class="ticket-info">
                                <h4>{{ $ticket->ticketAvailability->ticketType->name ?? 'General Admission' }}</h4>
                                <p class="location">{{ $ticket->ticketAvailability->visitSchedule->location->name ?? 'MET Museum' }}</p>
                                <p class="date">Visit Date: {{ optional($ticket->ticketAvailability->visitSchedule->visit_date)->format('M d, Y') ?? 'N/A' }}</p>
                                <p class="status">Status: <strong>{{ ucfirst($ticket->status) }}</strong></p>
                            </div>
                            <div class="ticket-qr">
                                <div class="qr-placeholder">{{ $ticket->qr_code }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-tickets">
                            <p>No tickets generated yet.</p>
                            @if(($order->payment?->payment_status ?? 'Pending') === 'Pending')
                                <small>Tickets will appear here after successful payment.</small>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
