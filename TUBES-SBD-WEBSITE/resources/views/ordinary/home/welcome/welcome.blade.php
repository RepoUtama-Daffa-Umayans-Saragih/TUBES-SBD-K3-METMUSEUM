@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/home/welcome/welcome.css')
@endpush

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Jelajahi Koleksi Seni Dunia</h1>
            <p class="hero-subtitle">Temukan karya-karya seni dari berbagai epoch dan budaya di The Metropolitan Museum</p>
            <div class="hero-actions">
                <a href="{{ route('art.index') }}" class="btn-primary">Jelajahi Koleksi</a>
                <a href="{{ route('ticket.admission') }}" class="btn-secondary">Pesan Tiket</a>
            </div>
        </div>
    </section>

    <!-- Collections Preview Section -->
    <section class="collections-section">
        <div class="section-header">
            <h2 class="section-title">Koleksi Utama</h2>
            <p class="section-description">Nikmati kurasi pilihan terbaik dari lebih dari 2 juta karya seni kami</p>
        </div>

        <div class="collections-grid">
            <!-- American Art Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-purple"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Seni Amerika</h3>
                    <p class="collection-meta">Karya seni dari seniman Amerika terkemuka</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>

            <!-- Asian Art Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-pink"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Seni Asia</h3>
                    <p class="collection-meta">Koleksi seni tradisional dan kontemporer Asia</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>

            <!-- European Art Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-cyan"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Seni Eropa</h3>
                    <p class="collection-meta">Lukisan dan patung dari Renaissance hingga era modern</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>

            <!-- Decorative Arts Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-peach"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Seni Dekoratif</h3>
                    <p class="collection-meta">Furnitur, keramik, dan kerajinan tangan berkualitas tinggi</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>

            <!-- Photography Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-mint"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Fotografi</h3>
                    <p class="collection-meta">Karya-karya fotografi ikonik dari para master</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>

            <!-- Ancient Art Card -->
            <div class="collection-card">
                <div class="collection-image collection-image-gradient-orange"></div>
                <div class="collection-content">
                    <h3 class="collection-title">Seni Kuno</h3>
                    <p class="collection-meta">Artefak dari peradaban kuno dunia</p>
                    <a href="{{ route('art.index') }}" class="collection-link">
                        Lihat Koleksi →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section-centered">
        <div class="container-max-800">
            <h2 class="section-title">Kunjungi The Met</h2>
            <p class="section-description mb-2rem">
                Jalani pengalaman seni yang tak terlupakan. Museum kami buka setiap hari untuk memberikan akses terbaik
                ke koleksi seni dunia.
            </p>
            <a href="{{ route('plan-your-visit.index') }}" class="btn-primary">Rencana Kunjungan Anda</a>
        </div>
    </section>

@endsection
