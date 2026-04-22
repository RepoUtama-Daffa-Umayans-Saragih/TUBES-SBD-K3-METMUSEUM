@extends('layouts.app')

@section('content')
@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/admission/admission.css')
@endpush
<div class="booking-page">
    <div class="booking-header">
        <h1 class="booking-title">Select Visit Date</h1>
        <p class="booking-subtitle">Choose a visit schedule to continue to ticket selection.</p>
    </div>

    <div class="booking-stepper" aria-label="Booking steps">
        <div class="booking-step is-active">Step 1: Select Date</div>
        <div class="booking-step">Step 2: Select Tickets</div>
        <div class="booking-step">Step 3: Add to Cart</div>
        <div class="booking-step">Step 4: View Cart</div>
        <div class="booking-step">Step 5: Checkout</div>
        <div class="booking-step">Step 6: Confirmation</div>
    </div>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    <div class="schedule-grid">
        @forelse($schedules as $schedule)
            <div class="schedule-card">
                <div class="schedule-card-title">{{ $schedule->location->name ?? 'Museum Location' }}</div>
                <div class="schedule-card-body">Visit date: {{ optional($schedule->visit_date)->format('F j, Y') }}</div>
                <div class="schedule-card-body">Capacity: {{ $schedule->capacity_limit }}</div>
                <div class="booking-actions">
                    <a href="{{ route('ticket.select', $schedule->id) }}" class="booking-btn">Select schedule</a>
                </div>
            </div>
        @empty
            <div class="booking-card">
                <p>No visit schedules available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection@extends('layouts.app')

@section('content')
<div class="booking-page">
    <div class="booking-header">
        <h1 class="booking-title">Select Visit Date</h1>
        <p class="booking-subtitle">Choose a visit schedule to continue to ticket selection.</p>
    </div>

    <div class="booking-stepper" aria-label="Booking steps">
        <div class="booking-step is-active">Step 1: Select Date</div>
        <div class="booking-step">Step 2: Select Tickets</div>
        <div class="booking-step">Step 3: Add to Cart</div>
        <div class="booking-step">Step 4: View Cart</div>
        <div class="booking-step">Step 5: Checkout</div>
        <div class="booking-step">Step 6: Confirmation</div>
    </div>

    @if(session('success'))
        <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    <div class="schedule-grid">
        @forelse($schedules as $schedule)
            <div class="schedule-card">
                <div class="schedule-card-title">{{ $schedule->location->name ?? 'Museum Location' }}</div>
                <div class="schedule-card-body">Visit date: {{ optional($schedule->visit_date)->format('F j, Y') }}</div>
                <div class="schedule-card-body">Capacity: {{ $schedule->capacity_limit }}</div>
                <div class="booking-actions">
                    <a href="{{ route('ticket.select', $schedule->id) }}" class="booking-btn">Select schedule</a>
                </div>
            </div>
        @empty
            <div class="booking-card">
                <p>No visit schedules available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
