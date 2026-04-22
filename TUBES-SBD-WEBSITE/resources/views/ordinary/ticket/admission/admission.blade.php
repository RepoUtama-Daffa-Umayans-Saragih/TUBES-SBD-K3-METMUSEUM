@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/ticket/admission/admission.css')
@endpush

@section('title', 'Admission Tickets - MET Museum')

@section('content')
<div class="admission-wrapper">
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="admission-header">
        <h1>Admission Tickets</h1>
        <p>Purchase tickets to visit The Metropolitan Museum of Art. All admission benefits support the museum's mission and operations.</p>
    </div>

    @if(isset($tickets) && count($tickets) > 0)
        <div class="tickets-grid">
            @foreach($tickets as $ticket)
                <div class="ticket-card @if($loop->first) featured @endif">
                    <div class="ticket-type">
                        <div class="ticket-category">{{ $ticket->location->name ?? 'General Admission' }}</div>
                        <h3>{{ $ticket->category }}</h3>
                        @if(Auth::check() && Auth::user()->is_membership)
                            @php
                                $memberPrice = $ticket->price * 0.9;
                            @endphp
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">

                                <div class="ticket-price">${{ number_format($memberPrice, 2) }}</div>
