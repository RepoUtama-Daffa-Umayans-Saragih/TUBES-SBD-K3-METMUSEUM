@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/about/about.css')
@endpush

@section('title', 'About Us')

@section('content')
<div class="about-page">
    <h1>About The Metropolitan Museum</h1>
    <p>Welcome to The Met! Discover the history, mission, and vision of one of the world's largest and finest art museums.</p>
</div>
@endsection
