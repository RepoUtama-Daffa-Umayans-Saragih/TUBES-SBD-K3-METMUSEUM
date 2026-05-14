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
                <img src="{{ asset('storage/' . $primaryImage->image_url) }}" alt="{{ $artwork->title }}" class="main-image" id="mainImage">
            @else
                <div class="main-image" style="background-color: #e8e8e8;"></div>
            @endif

            @if($allImages->count() > 1)
                <div class="image-gallery">
                    @foreach($allImages as $image)
                        <img
                            src="{{ asset('storage/' . $image->image_url) }}"
                            alt="{{ $artwork->title }}"
                            class="gallery-thumbnail {{ $image->is_primary ? 'active' : '' }}"
                            data-src="{{ asset('storage/' . $image->image_url) }}"
                            onclick="document.getElementById('mainImage').src = this.dataset.src; document.querySelectorAll('.gallery-thumbnail').forEach(el => el.classList.remove('active')); this.classList.add('active');"
                        >
                    @endforeach
                </div>
            @endif
        </div>

        <div class="detail-info">
            <h1 class="detail-title">{{ $artwork->title }}</h1>

            <div class="detail-meta">
                @if($artwork->constituents->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Attribution</span>
                        <span class="meta-value">
                            @foreach($artwork->constituents as $constituent)
                                <div class="constituent-line">
                                    @if($constituent->pivot->role) <span class="role">{{ $constituent->pivot->role->name }}:</span> @endif
                                    @if($constituent->pivot->prefix) <span class="prefix">{{ $constituent->pivot->prefix->name }}</span> @endif
                                    <span class="name">{{ $constituent->display_name }}</span>
                                    @if($constituent->pivot->suffix) <span class="suffix">{{ $constituent->pivot->suffix->name }}</span> @endif
                                </div>
                            @endforeach
                        </span>
                    </div>
                @endif

                @if($artwork->object_date_display)
                    <div class="meta-item">
                        <span class="meta-label">Date</span>
                        <span class="meta-value">{{ $artwork->object_date_display }}</span>
                    </div>
                @elseif($artwork->object_begin_date || $artwork->object_end_date)
                    <div class="meta-item">
                        <span class="meta-label">Date</span>
                        <span class="meta-value">
                            @if($artwork->object_begin_date && $artwork->object_end_date)
                                {{ $artwork->object_begin_date }} - {{ $artwork->object_end_date }}
                            @elseif($artwork->object_begin_date)
                                {{ $artwork->object_begin_date }}
                            @else
                                {{ $artwork->object_end_date }}
                            @endif
                        </span>
                    </div>
                @endif

                @if($artwork->mediums->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Medium</span>
                        <span class="meta-value">{{ $artwork->mediums->pluck('name')->join(', ') }}</span>
                    </div>
                @endif

                @if($artwork->geographies->isNotEmpty())
                    <div class="meta-item">
                        <span class="meta-label">Geography</span>
                        <span class="meta-value">
                            @foreach($artwork->geographies as $geo)
                                <div class="geography-line">
                                    <strong>{{ optional($geo->geographyType)->name ?? 'Geography' }}:</strong> 
                                    {{ implode(', ', array_filter([optional($geo->city)->name, optional($geo->state)->name, optional($geo->country)->name])) }}
                                </div>
                            @endforeach
                        </span>
                    </div>
                @endif
                
                @if($artwork->creditLine)
                    <div class="meta-item">
                        <span class="meta-label">Credit Line</span>
                        <span class="meta-value">{{ $artwork->creditLine->credit_line_text }}</span>
                    </div>
                @endif
                
                @if($artwork->dimensions_display)
                    <div class="meta-item">
                        <span class="meta-label">Dimensions</span>
                        <span class="meta-value">{{ $artwork->dimensions_display }}</span>
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
            
            @if($artwork->provenance)
                <div class="detail-description" style="margin-top: 1.5rem;">
                    <h3 class="description-label">Provenance</h3>
                    <div class="description-text">
                        {!! nl2br(e($artwork->provenance)) !!}
                    </div>
                </div>
            @endif

            @if($artwork->artWorkSims && $artwork->artWorkSims->isNotEmpty())
                <div class="detail-description" style="margin-top: 1.5rem;">
                    <h3 class="description-label">Signatures, Inscriptions, and Markings</h3>
                    <div class="description-text">
                        @foreach($artwork->artWorkSims as $sim)
                            <div style="margin-bottom: 0.5rem;">
                                <strong>{{ $sim->sim_type }}:</strong> 
                                {{ $sim->sim_text }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if($artwork->exhibitionHistories->isNotEmpty())
                <div class="detail-description" style="margin-top: 1.5rem;">
                    <h3 class="description-label">Exhibition History</h3>
                    <ul class="description-text" style="list-style-type: disc; padding-left: 20px;">
                        @foreach($artwork->exhibitionHistories as $exhibition)
                            <li>{{ $exhibition->exhibition_text }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if($artwork->references->isNotEmpty())
                <div class="detail-description" style="margin-top: 1.5rem;">
                    <h3 class="description-label">References</h3>
                    <ul class="description-text" style="list-style-type: disc; padding-left: 20px;">
                        @foreach($artwork->references as $reference)
                            <li>{{ $reference->reference_text }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('art.index') }}" class="back-link">Back to Collection</a>
        </div>
    </div>
</div>
@endsection
