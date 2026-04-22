@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/ticket/ticket.css')
@endpush

@section('title', 'Tickets - MET Museum')

@section('content')
<div class="tickets-container">
    <div class="tickets-header">
        <h1>Visit the Museum</h1>
        <p>Select a ticket type and visit date to begin your order</p>
    </div>

    @forelse($groupedTickets as $locationId => $locationData)
        <div class="location-section">
            <div class="location-title">📍 {{ $locationData['location']->name }}</div>

            @forelse($locationData['categories'] as $category => $categoryTickets)
                <div class="category-group">
                    <div class="category-title">{{ $category }}</div>
                    <div class="ticket-grid">
                        @foreach($categoryTickets as $ticket)
                            <div class="ticket-card">
                                <div class="ticket-name">{{ $ticket->category }}</div>
                                <div class="ticket-price">${{ number_format($ticket->price, 2) }}</div>
                                <form action="{{ route('order.create') }}" method="GET" class="form-inline">
                                    <input type="hidden" name="ticket_id" value="{{ $ticket->ticket_id }}">
                                    <button type="submit" class="select-button">Select & Continue</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted-light">No tickets available for this location.</p>
            @endforelse
        </div>
    @empty
        <div class="section-centered-padded">
            <p class="text-muted-light">No tickets available at this time.</p>
        </div>
    @endforelse
</div>
@endsection
