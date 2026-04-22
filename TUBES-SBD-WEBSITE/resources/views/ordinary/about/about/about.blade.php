@extends('layout')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/about/about/about.css')
@endpush

@section('title', 'aboutpage')

@section('content')
    <div>
        ini adalah halaman about
    </div>
@endsection
