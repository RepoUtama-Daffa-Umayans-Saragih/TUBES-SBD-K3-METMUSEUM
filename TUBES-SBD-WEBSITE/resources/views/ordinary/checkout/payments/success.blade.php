@extends('layouts.app')

@section('content')
@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/checkout/payments/success.css')
@endpush
<div class="booking-page">
    @php
        $isMembership = $order->payment?->payment_method === 'Membership';
        $paidStatus = strtolower((string) ($order->payment?->payment_status ?? 'pending'));
    @endphp

    <section class="success-hero">
        <div class="success-hero-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"/>
                <path d="M7 12.5L10.2 15.7L17.2 8.7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1 class="success-hero-title">{{ $isMembership ? 'Membership Confirmed' : 'Booking Confirmed' }}</h1>

        <p class="success-hero-subtitle">
            @if($isMembership)
                Your membership payment has been received successfully.
            @else
                Your order has been created successfully. Please keep your QR codes safe for check-in.
            @endif
        </p>

        @if($isMembership)
            <p class="success-hero-note">A confirmation email has been sent to your email address.</p>
        @endif
    </section>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    <div class="success-layout">
        <section class="content-grid">
            <article class="booking-card premium-card">
                <div class="booking-card-title">Order Information</div>

                <div class="summary-list summary-grid-two">
                    <div class="summary-row">
                        <div class="summary-title-line">Order Code</div>
                        <div class="summary-subline">{{ $order->order_code }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-title-line">Payment Status</div>
                        <div class="summary-subline">
                            <span class="status-badge {{ $paidStatus }}">{{ $order->payment?->payment_status ?? 'Pending' }}</span>
                        </div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-title-line">Order Date</div>
                        <div class="summary-subline">{{ optional($order->order_date)->format('F j, Y H:i') }}</div>
                    </div>

                    <div class="summary-row">
                        <div class="summary-title-line">Paid At</div>
                        <div class="summary-subline">{{ optional($order->payment?->paid_at)->format('F j, Y H:i') }}</div>
                    </div>
                </div>
            </article>

            @if($isMembership)
                <article class="booking-card premium-card">
                    <div class="booking-card-title">Membership Information</div>

                    <div class="summary-list summary-grid-two">
                        <div class="summary-row">
                            <div class="summary-title-line">Membership</div>
                            <div class="summary-subline">MET Membership</div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-title-line">Membership Status</div>
                            <div class="summary-subline">Not Activated Yet</div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-title-line">Premium Started At</div>
                            <div class="summary-subline muted">Not Activated Yet</div>
                        </div>

                        <div class="summary-row">
                            <div class="summary-title-line">Premium Expired At</div>
                            <div class="summary-subline muted">Not Activated Yet</div>
                        </div>
                    </div>

                    <p class="helper-text">Please activate your membership from the email link.</p>
                </article>

                <article class="booking-card premium-card steps-card">
                    <div class="booking-card-title">What Happens Next?</div>

                    <ol class="steps-list">
                        <li class="step-item">
                            <span class="step-index">1</span>
                            <div class="step-text">Check your email</div>
                        </li>
                        <li class="step-item">
                            <span class="step-index">2</span>
                            <div class="step-text">Activate your membership</div>
                        </li>
                        <li class="step-item">
                            <span class="step-index">3</span>
                            <div class="step-text">Enjoy premium museum access</div>
                        </li>
                    </ol>
                </article>
            @else
                <div class="success-grid">
                    @foreach($order->tickets as $ticket)
                        <article class="success-ticket">
                            <div class="booking-card-title">{{ $ticket->ticketAvailability->ticketType->name ?? 'Ticket' }}</div>
                            <div class="booking-card-meta">{{ $ticket->ticketAvailability->visitSchedule->location->name ?? '-' }} · {{ optional($ticket->ticketAvailability->visitSchedule->visit_date)->format('F j, Y') }}</div>
                            <div class="booking-card-meta">Status: <span class="status-badge {{ strtolower($ticket->status) }}">{{ ucfirst($ticket->status) }}</span></div>
                            <div class="qr-box">
                                <div class="qr-image-wrap">
                                    {!! QrCode::size(140)->generate($ticket->qr_code) !!}
                                </div>
                                {{ $ticket->qr_code }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="booking-actions">
            @if($isMembership)
                <a href="{{ route('membership.index') }}" class="booking-btn">Explore memberships</a>
                <a href="{{ route('order.show') }}" class="booking-btn-outline">View My Orders</a>
                <a href="{{ route('ticket.admission') }}" class="booking-btn-outline">Go to Admission</a>
            @else
                <a href="{{ route('ticket.admission') }}" class="booking-btn">Book another ticket</a>
                <a href="{{ route('order.show') }}" class="booking-btn-outline">View My Orders</a>
            @endif
        </section>
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
