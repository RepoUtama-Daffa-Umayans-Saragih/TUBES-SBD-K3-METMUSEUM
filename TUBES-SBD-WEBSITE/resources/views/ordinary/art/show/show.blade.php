@extends('layouts.app')

@section('content')
@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/show/show.css')
@endpush
<div class="artwork-detail-page">
    <div class="container">
        <a href="{{ route('art.index') }}" class="back-link">← Back to Collections</a>

        <div class="artwork-detail-content">
            <!-- Artwork Gallery -->
            <div class="artwork-gallery-section">
                <div class="artwork-gallery">
                    @if(isset($artwork) && isset($artwork['image_url']))
                        <img
                            src="{{ $artwork['image_url'] }}"
                            alt="{{ $artwork['title'] ?? 'Artwork' }}"
                            class="artwork-main-image"
                        >
                    @else
                        <div class="no-image">No image available</div>
                    @endif
                </div>
            </div>

            <!-- Artwork Details -->
            <div class="artwork-details-section">
                <div class="artwork-header">
                    <h1>{{ $artwork['title'] ?? 'Artwork Title' }}</h1>
                    <p class="artist-name">by {{ $artwork['artist_name'] ?? 'Unknown Artist' }}</p>
                </div>

                <!-- Metadata -->
                <div class="artwork-meta-section">
                    <div class="meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Artist</span>
                            <span class="meta-value">{{ $artwork['artist_name'] ?? 'Unknown' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Date</span>
                            <span class="meta-value">{{ $artwork['date'] ?? 'Unknown' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Medium</span>
                            <span class="meta-value">{{ $artwork['medium'] ?? 'Unknown' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Department</span>
                            <span class="meta-value">{{ $artwork['department'] ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="artwork-description-section">
                    <h3>About This Artwork</h3>
                    <p>{{ $artwork['description'] ?? 'Description not available.' }}</p>
                </div>

                <!-- Actions -->
                <div class="artwork-actions">
                    <button class="btn btn-primary" onclick="alert('Share functionality coming soon!')">Share</button>
                    <button class="btn btn-secondary" onclick="alert('Save functionality coming soon!')">Save to Collection</button>
                    <a href="{{ route('art.index') }}" class="btn btn-outline">Back to Collections</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
