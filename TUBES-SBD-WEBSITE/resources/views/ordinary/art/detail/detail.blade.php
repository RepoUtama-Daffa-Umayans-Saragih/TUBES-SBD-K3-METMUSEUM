@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/detail/detail.css')
@endpush

@section('title', $artwork->title . ' - MET Museum')
    }

    .meta-value {
        font-size: 0.95rem;
        color: #666;
    }

    .meta-value a {
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #ddd;
    }

    .meta-value a:hover {
        border-bottom-color: #000;
    }

    .detail-description {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e0e0e0;
    }

    .description-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    .description-text {
        font-size: 0.95rem;
        line-height: 1.7;
        color: #555;
    }

    .back-link {
        display: inline-block;
        margin-top: 2rem;
        padding: 0.7rem 1.5rem;
        border: 1px solid #ddd;
        text-decoration: none;
        color: #333;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .back-link:hover {
        border-color: #000;
        background-color: #f5f5f5;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .detail-title {
            font-size: 1.5rem;
        }

        .meta-item {
            grid-template-columns: 120px 1fr;
        }

        .image-gallery {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
@endsection

@section('content')
<div class="detail-container">
    <div class="breadcrumb">
        <a href="/art/collection">Collection</a> / <span>{{ $artwork->title }}</span>
    </div>

    <div class="detail-grid">
        <!-- Image Section -->
        <div class="detail-image-section">
            @php
                $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
                $allImages = $artwork->images;
            @endphp

            @if($primaryImage)
                <img src="{{ $primaryImage->url }}" alt="{{ $artwork->title }}" class="main-image" id="mainImage">
            @else
                <div class="main-image" style="background-color: #e8e8e8;"></div>
            @endif

            @if($allImages->count() > 1)
                <div class="image-gallery">
                    @foreach($allImages as $image)
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $artwork->title }}"
                            class="gallery-thumbnail {{ $image->is_primary ? 'active' : '' }}"
                            data-src="{{ $image->url }}"
                            onclick="document.getElementById('mainImage').src = this.dataset.src; document.querySelectorAll('.gallery-thumbnail').forEach(el => el.classList.remove('active')); this.classList.add('active');"
                        >
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="detail-info">
            <h1 class="detail-title">{{ $artwork->title }}</h1>

            <div class="detail-meta">
                @if($artwork->artists->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Artist{{ $artwork->artists->count() > 1 ? 's' : '' }}</span>
                        <span class="meta-value">
                            @foreach($artwork->artists as $artist)
                                <div>{{ $artist->name }}
                                    @if($artist->nationality)
                                        <span class="text-muted-light">({{ $artist->nationality }})</span>
                                    @endif
                                </div>
                            @endforeach
                        </span>
                    </div>
                @endif

                @if($artwork->year_start || $artwork->year_end)
                    <div class="meta-item">
                        <span class="meta-label">Date</span>
                        <span class="meta-value">
                            @if($artwork->year_start && $artwork->year_end)
                                {{ $artwork->year_start }} – {{ $artwork->year_end }}
                            @elseif($artwork->year_start)
                                {{ $artwork->year_start }}
                            @else
                                {{ $artwork->year_end }}
                            @endif
                        </span>
                    </div>
                @endif

                @if($artwork->department)
                    <div class="meta-item">
                        <span class="meta-label">Department</span>
                        <span class="meta-value">{{ $artwork->department->name }}</span>
                    </div>
                @endif

                @if($artwork->objectType)
                    <div class="meta-item">
                        <span class="meta-label">Type</span>
                        <span class="meta-value">{{ $artwork->objectType->name }}</span>
                    </div>
                @endif

                @if($artwork->geoLocation)
                    <div class="meta-item">
                        <span class="meta-label">Geography</span>
                        <span class="meta-value">{{ $artwork->geoLocation->name }}</span>
                    </div>
                @endif

                @if($artwork->location)
                    <div class="meta-item">
                        <span class="meta-label">Gallery</span>
                        <span class="meta-value">{{ $artwork->location->name }}</span>
                    </div>
                @endif

                @if($artwork->object_number)
                    <div class="meta-item">
                        <span class="meta-label">Object Number</span>
                        <span class="meta-value">{{ $artwork->object_number }}</span>
                    </div>
                @endif
            </div>

            @if($artwork->description)
                <div class="detail-description">
                    <h3 class="description-label">Description</h3>
                    <div class="description-text">
                        {!! nl2br(e($artwork->description)) !!}
                    </div>
                </div>
            @endif

            <a href="/art/collection" class="back-link">← Back to Collection</a>
        </div>
    </div>
</div>
@endsection
