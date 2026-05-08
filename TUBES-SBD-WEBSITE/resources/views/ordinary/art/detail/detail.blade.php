@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/detail/detail.css')
@endpush

@section('title', $artwork->title . ' - MET Museum')

@section('content')
<div class="detail-container">
    <div class="breadcrumb">
        <a href="{{ route('art.index') }}">Collection</a> / <span>{{ $artwork->title }}</span>
    </div>

    <div class="detail-grid">
        <div class="detail-image-section">
            @php
                $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
                $allImages = $artwork->images;
            @endphp

            @if($primaryImage)
                <img src="{{ asset('storage/' . $primaryImage->url) }}" alt="{{ $artwork->title }}" class="main-image" id="mainImage">
            @else
                <div class="main-image" style="background-color: #e8e8e8;"></div>
            @endif

            @if($allImages->count() > 1)
                <div class="image-gallery">
                    @foreach($allImages as $image)
                        <img
                            src="{{ asset('storage/' . $image->url) }}"
                            alt="{{ $artwork->title }}"
                            class="gallery-thumbnail {{ $image->is_primary ? 'active' : '' }}"
                            data-src="{{ asset('storage/' . $image->url) }}"
                            onclick="document.getElementById('mainImage').src = this.dataset.src; document.querySelectorAll('.gallery-thumbnail').forEach(el => el.classList.remove('active')); this.classList.add('active');"
                        >
                    @endforeach
                </div>
            @endif
        </div>

        <div class="detail-info">
            <h1 class="detail-title">{{ $artwork->title }}</h1>

            <div class="detail-meta">
                @if($artwork->artists->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Artist{{ $artwork->artists->count() > 1 ? 's' : '' }}</span>
                        <span class="meta-value">{{ $artwork->artists->pluck('name')->join(', ') }}</span>
                    </div>
                @endif

                @if($artwork->year_start || $artwork->year_end)
                    <div class="meta-item">
                        <span class="meta-label">Date</span>
                        <span class="meta-value">
                            @if($artwork->year_start && $artwork->year_end)
                                {{ $artwork->year_start }} - {{ $artwork->year_end }}
                            @elseif($artwork->year_start)
                                {{ $artwork->year_start }}
                            @else
                                {{ $artwork->year_end }}
                            @endif
                        </span>
                    </div>
                @endif

                @if($artwork->met_object_id)
                    <div class="meta-item">
                        <span class="meta-label">Met Object ID</span>
                        <span class="meta-value">{{ $artwork->met_object_id }}</span>
                    </div>
                @endif

                @if($artwork->object_number)
                    <div class="meta-item">
                        <span class="meta-label">Object Number</span>
                        <span class="meta-value">{{ $artwork->object_number }}</span>
                    </div>
                @endif

                @if($artwork->accession_year)
                    <div class="meta-item">
                        <span class="meta-label">Accession Year</span>
                        <span class="meta-value">{{ $artwork->accession_year }}</span>
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

                @if($artwork->location)
                    <div class="meta-item">
                        <span class="meta-label">Gallery</span>
                        <span class="meta-value">{{ $artwork->location->name }}</span>
                    </div>
                @endif

                @if($artwork->repository)
                    <div class="meta-item">
                        <span class="meta-label">Repository</span>
                        <span class="meta-value">{{ $artwork->repository->name }}</span>
                    </div>
                @endif

                @if($artwork->classification)
                    <div class="meta-item">
                        <span class="meta-label">Classification</span>
                        <span class="meta-value">{{ $artwork->classification->name }}</span>
                    </div>
                @endif

                @if($artwork->materials->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Materials</span>
                        <span class="meta-value">{{ $artwork->materials->pluck('name')->join(', ') }}</span>
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

            <a href="{{ route('art.index') }}" class="back-link">Back to Collection</a>
        </div>
    </div>
</div>
@endsection
