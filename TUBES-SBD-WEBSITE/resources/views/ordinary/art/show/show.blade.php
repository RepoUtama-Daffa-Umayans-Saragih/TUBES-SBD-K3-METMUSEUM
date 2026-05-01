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
            @php
                $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
            @endphp
            <!-- Artwork Gallery -->
            <div class="artwork-gallery-section">
                <div class="artwork-gallery">
                    @if($primaryImage)
                        <img
                            src="{{ asset('storage/' . $primaryImage->url) }}"
                            alt="{{ $artwork->title }}"
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
                    <h1>{{ $artwork->title }}</h1>
                    <p class="artist-name">by {{ $artwork->artists->pluck('name')->join(', ') ?: 'Unknown Artist' }}</p>
                </div>

                <!-- Metadata -->
                <div class="artwork-meta-section">
                    <div class="meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Artist</span>
                            <span class="meta-value">{{ $artwork->artists->pluck('name')->join(', ') ?: 'Unknown' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Date</span>
                            <span class="meta-value">
                                @if($artwork->year_start && $artwork->year_end)
                                    {{ $artwork->year_start }} - {{ $artwork->year_end }}
                                @elseif($artwork->year_start)
                                    {{ $artwork->year_start }}
                                @elseif($artwork->year_end)
                                    {{ $artwork->year_end }}
                                @else
                                    Unknown
                                @endif
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Object Type</span>
                            <span class="meta-value">{{ $artwork->objectType?->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Department</span>
                            <span class="meta-value">{{ $artwork->department?->name ?? 'Unknown' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="artwork-description-section">
                    <h3>About This Artwork</h3>
                    <p>{{ $artwork->description ?? 'Description not available.' }}</p>
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
