@extends('layouts.app')

@section('content')
@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/checkout/payments/success.css')
@endpush
<div class="booking-page">
    <div class="booking-header">
        <h1 class="booking-title">Booking Confirmed</h1>
        <p class="booking-subtitle">Your order has been created successfully. Please keep your QR codes safe for check-in.</p>
    </div>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    <div class="success-layout">
        <div class="booking-card">
            <div class="booking-card-title">Order information</div>
            <div class="summary-list">
                <div class="summary-row">
                    <div class="summary-title-line">Order code</div>
                    <div class="summary-subline">{{ $order->order_code }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-title-line">Payment status</div>
                    <div class="summary-subline">{{ $order->payment?->payment_status ?? 'Pending' }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-title-line">Order date</div>
                    <div class="summary-subline">{{ optional($order->order_date)->format('F j, Y H:i') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-title-line">Paid at</div>
                    <div class="summary-subline">{{ optional($order->payment?->paid_at)->format('F j, Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="success-grid">
            @foreach($order->tickets as $ticket)
                <div class="success-ticket">
                    <div class="booking-card-title">{{ $ticket->ticketAvailability->ticketType->name ?? 'Ticket' }}</div>
                    <div class="booking-card-meta">{{ $ticket->ticketAvailability->visitSchedule->location->name ?? '-' }} · {{ optional($ticket->ticketAvailability->visitSchedule->visit_date)->format('F j, Y') }}</div>
                    <div class="booking-card-meta">Status: {{ ucfirst($ticket->status) }}</div>
                    <div class="qr-box">{{ $ticket->qr_code }}</div>
                </div>
            @endforeach
        </div>

        <div class="booking-actions">
            <a href="{{ route('ticket.admission') }}" class="booking-btn">Book another ticket</a>
            <a href="{{ route('order.show') }}" class="booking-btn-outline">View My Orders</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Replace the current history state to clear out the payment page from the back stack
    history.replaceState(null, null, window.location.href);
    // Push a new state so there is a "forward" history to pop
    history.pushState(null, null, window.location.href);

    // Intercept back-button navigation
    window.onpopstate = function () {
        window.location.replace("{{ route('order.show') }}");
    };
</script>
@endpush
@endsection
