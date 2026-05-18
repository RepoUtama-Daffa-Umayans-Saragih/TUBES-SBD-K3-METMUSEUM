@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/show/show.css')
@endpush

@section('content')

@php
    $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
    $allImages = $artwork->images;

    // Constituents grouped by role
    $makers = $artwork->constituents->where('role', 'Maker');
    $artists = $artwork->constituents->where('role', 'Artist');
    $allConstituents = $artwork->constituents;

    // Date string
    $dateStr = '';
    if ($artwork->year_start && $artwork->year_end && $artwork->year_start != $artwork->year_end) {
        $dateStr = $artwork->year_start . '–' . $artwork->year_end;
    } elseif ($artwork->year_start) {
        $dateStr = $artwork->year_start;
    } elseif ($artwork->year_end) {
        $dateStr = $artwork->year_end;
    } elseif ($artwork->object_date) {
        $dateStr = $artwork->object_date;
    }

    // Gallery info
    $galleryText = '';
    if ($artwork->location) {
        $galleryText = $artwork->location;
        if ($artwork->gallery_number) {
            $galleryText .= ' in Gallery ' . $artwork->gallery_number;
        }
    }

    // Cultures
    $cultures = $artwork->cultures->pluck('name')->join(', ');

    // Medium
    $mediumName = $artwork->medium?->name ?? '';

    // Measurements / dimensions
    $dimensions = '';
    if ($artwork->measurements->isNotEmpty()) {
        $dims = $artwork->measurements->map(function($m) {
            $parts = [];
            if ($m->element_name) $parts[] = $m->element_name . ':';
            if ($m->element_description) $parts[] = $m->element_description;
            return implode(' ', $parts);
        })->filter()->join('; ');
        $dimensions = $dims ?: '';
    }

    // Classification
    $classificationName = $artwork->classification?->name ?? '';

    // Credit line
    $creditLine = $artwork->creditLine?->description ?? $artwork->credit_line ?? '';

    // Department
    $departmentName = $artwork->department?->name ?? '';

    // Accession / object number
    $objectNumber = $artwork->accession_number ?? '';

    // SIMs
    $sims = $artwork->sims ?? collect();
    $signatures = $sims->where('type', 'Signature');
    $inscriptions = $sims->where('type', 'Inscription');
    $markings = $sims->where('type', 'Marking');

    // Exhibition histories
    $exhibitions = $artwork->exhibitionHistories ?? collect();
@endphp

<div class="met-artwork-page">

    {{-- ============================================================
         HERO SECTION
    ============================================================ --}}
    <div class="met-hero">
        <div class="met-container">

            {{-- LEFT: Info --}}
            <div class="met-hero__left">

                <a href="{{ route('art.index') }}" class="met-back-link">← Back to Collections</a>

                <h1 class="met-title">{{ $artwork->title }}</h1>

                {{-- Constituents --}}
                <div class="met-makers">
                    @foreach($allConstituents as $constituent)
                        <div class="met-maker-line">
                            @if($constituent->role && $constituent->role !== 'Artist')
                                <span class="met-maker-role">{{ $constituent->role }}:</span>
                            @endif
                            <a href="#" class="met-maker-name">{{ $constituent->name }}</a>
                            @if($constituent->nationality)
                                <span class="met-maker-nat">{{ $constituent->nationality }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if($allConstituents->isEmpty())
                        <div class="met-maker-line">
                            <span class="met-maker-name">Unknown Artist</span>
                        </div>
                    @endif
                </div>

                @if($dateStr)
                    <div class="met-date">{{ $dateStr }}</div>
                @endif

                @if($galleryText)
                    <div class="met-gallery-info">
                        <span class="met-gallery-pin">&#9679;</span>
                        On view at <strong>{{ $galleryText }}</strong>
                    </div>
                @endif

                @if($artwork->description)
                    <div class="met-description" x-data="{ expanded: false }">
                        <div class="met-description__text" :class="{ 'met-description__text--expanded': expanded }">
                            {{ $artwork->description }}
                        </div>
                        <button class="met-view-more" @click="expanded = !expanded" x-text="expanded ? 'View less ∧' : 'View more ∨'">View more ∨</button>
                    </div>
                @endif

            </div>

            {{-- RIGHT: Gallery --}}
            <div class="met-hero__right" x-data="artGallery()">

                <div class="met-main-image-wrap">
                    @if($primaryImage)
                        <img
                            :src="currentImage"
                            alt="{{ $artwork->title }}"
                            class="met-main-image"
                            x-ref="mainImg"
                        >
                    @else
                        <div class="met-no-image">No image available</div>
                    @endif
                </div>

                <div class="met-image-meta">
                    @if($artwork->is_public_domain)
                        <span class="met-public-domain">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
                            Public Domain
                        </span>
                    @else
                        <span></span>
                    @endif

                    <div class="met-image-actions">
                        <button class="met-icon-btn" title="Download" onclick="window.open('{{ $primaryImage ? asset('storage/' . $primaryImage->url) : '#' }}')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        </button>
                        <button class="met-icon-btn" title="Share" onclick="navigator.share ? navigator.share({title: '{{ addslashes($artwork->title) }}', url: window.location.href}) : alert('Share: ' + window.location.href)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </button>
                        <button class="met-icon-btn" title="Fullscreen" @click="openFullscreen()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                        </button>
                    </div>
                </div>

                @if($allImages->count() > 1)
                    <div class="met-thumbnails">
                        @foreach($allImages as $image)
                            <button
                                class="met-thumb-btn"
                                :class="{ 'met-thumb-btn--active': currentImage === '{{ asset('storage/' . $image->url) }}' }"
                                @click="setImage('{{ asset('storage/' . $image->url) }}')"
                                type="button"
                            >
                                <img src="{{ asset('storage/' . $image->url) }}" alt="{{ $artwork->title }}" class="met-thumb-img">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Fullscreen modal --}}
                <div class="met-fullscreen-modal" x-show="fullscreen" @click="fullscreen=false" x-cloak>
                    <img :src="currentImage" alt="{{ $artwork->title }}" class="met-fullscreen-img" @click.stop>
                    <button class="met-fullscreen-close" @click="fullscreen=false">✕</button>
                </div>

            </div>

        </div>
    </div>

    {{-- ============================================================
         ARTWORK DETAILS SECTION
    ============================================================ --}}
    <div class="met-details-section" x-data="{ activeTab: 'overview' }">
        <div class="met-container">

            <h2 class="met-details-title">Artwork Details</h2>

            <div class="met-details-layout">

                {{-- Sidebar --}}
                <nav class="met-sidebar">
                    <button
                        class="met-sidebar__item"
                        :class="{ 'met-sidebar__item--active': activeTab === 'overview' }"
                        @click="activeTab = 'overview'"
                        type="button"
                    >Overview</button>

                    <button
                        class="met-sidebar__item"
                        :class="{ 'met-sidebar__item--active': activeTab === 'sims' }"
                        @click="activeTab = 'sims'"
                        type="button"
                    >Signatures, Inscriptions, and Markings</button>

                    <button
                        class="met-sidebar__item"
                        :class="{ 'met-sidebar__item--active': activeTab === 'provenance' }"
                        @click="activeTab = 'provenance'"
                        type="button"
                    >Provenance</button>

                    <button
                        class="met-sidebar__item"
                        :class="{ 'met-sidebar__item--active': activeTab === 'exhibition' }"
                        @click="activeTab = 'exhibition'"
                        type="button"
                    >Exhibition History</button>
                </nav>

                {{-- Content Panel --}}
                <div class="met-tab-content">

                    {{-- OVERVIEW --}}
                    <div x-show="activeTab === 'overview'" x-transition:enter="met-fade-in">
                        <dl class="met-detail-list">

                            <div class="met-detail-row">
                                <dt class="met-detail-label">Title:</dt>
                                <dd class="met-detail-value">{{ $artwork->title }}</dd>
                            </div>

                            @if($makers->isNotEmpty())
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Maker:</dt>
                                    <dd class="met-detail-value">
                                        @foreach($makers as $maker)
                                            {{ $maker->name }}@if($maker->nationality) ({{ $maker->nationality }})@endif<br>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif

                            @if($artists->isNotEmpty())
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Artist:</dt>
                                    <dd class="met-detail-value">
                                        @foreach($artists as $artist)
                                            {{ $artist->name }}@if($artist->nationality) ({{ $artist->nationality }})@endif<br>
                                        @endforeach
                                    </dd>
                                </div>
                            @elseif($makers->isEmpty() && $allConstituents->isNotEmpty())
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Artist:</dt>
                                    <dd class="met-detail-value">
                                        @foreach($allConstituents as $c)
                                            {{ $c->name }}<br>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif

                            @if($dateStr)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Date:</dt>
                                    <dd class="met-detail-value">{{ $dateStr }}</dd>
                                </div>
                            @endif

                            @if($cultures)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Culture:</dt>
                                    <dd class="met-detail-value">{{ $cultures }}</dd>
                                </div>
                            @endif

                            @if($mediumName)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Medium:</dt>
                                    <dd class="met-detail-value">{{ $mediumName }}</dd>
                                </div>
                            @endif

                            @if($dimensions)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Dimensions:</dt>
                                    <dd class="met-detail-value">{{ $dimensions }}</dd>
                                </div>
                            @endif

                            @if($classificationName)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Classification:</dt>
                                    <dd class="met-detail-value">{{ $classificationName }}</dd>
                                </div>
                            @endif

                            @if($creditLine)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Credit Line:</dt>
                                    <dd class="met-detail-value">{{ $creditLine }}</dd>
                                </div>
                            @endif

                            @if($objectNumber)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Object Number:</dt>
                                    <dd class="met-detail-value">{{ $objectNumber }}</dd>
                                </div>
                            @endif

                            @if($departmentName)
                                <div class="met-detail-row">
                                    <dt class="met-detail-label">Curatorial Department:</dt>
                                    <dd class="met-detail-value">{{ $departmentName }}</dd>
                                </div>
                            @endif

                        </dl>
                    </div>

                    {{-- SIGNATURES, INSCRIPTIONS, AND MARKINGS --}}
                    <div x-show="activeTab === 'sims'" x-transition:enter="met-fade-in">
                        @php $hasSims = $signatures->isNotEmpty() || $inscriptions->isNotEmpty() || $markings->isNotEmpty(); @endphp
                        @if($hasSims)
                            <dl class="met-detail-list">
                                @foreach($signatures as $sim)
                                    <div class="met-detail-row">
                                        <dt class="met-detail-label">Signature:</dt>
                                        <dd class="met-detail-value">{{ $sim->description }}</dd>
                                    </div>
                                @endforeach
                                @foreach($inscriptions as $sim)
                                    <div class="met-detail-row">
                                        <dt class="met-detail-label">Inscription:</dt>
                                        <dd class="met-detail-value">{{ $sim->description }}</dd>
                                    </div>
                                @endforeach
                                @foreach($markings as $sim)
                                    <div class="met-detail-row">
                                        <dt class="met-detail-label">Marking:</dt>
                                        <dd class="met-detail-value">{{ $sim->description }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        @else
                            <p class="met-empty-state">No signatures, inscriptions, or markings available.</p>
                        @endif
                    </div>

                    {{-- PROVENANCE --}}
                    <div x-show="activeTab === 'provenance'" x-transition:enter="met-fade-in">
                        @if($artwork->provenance)
                            <div class="met-provenance-text">{{ $artwork->provenance }}</div>
                        @else
                            <p class="met-empty-state">No provenance information available.</p>
                        @endif
                    </div>

                    {{-- EXHIBITION HISTORY --}}
                    <div x-show="activeTab === 'exhibition'" x-transition:enter="met-fade-in">
                        @if($exhibitions->isNotEmpty())
                            <ul class="met-exhibition-list">
                                @foreach($exhibitions as $ex)
                                    <li class="met-exhibition-item">
                                        <div class="met-ex-title">{{ $ex->title ?? $ex->exhibition_title ?? '' }}</div>
                                        @if(!empty($ex->venue))
                                            <div class="met-ex-venue">{{ $ex->venue }}@if(!empty($ex->city)), {{ $ex->city }}@endif</div>
                                        @endif
                                        @if(!empty($ex->date_begin) || !empty($ex->date_end))
                                            <div class="met-ex-date">
                                                {{ $ex->date_begin ?? '' }}@if(!empty($ex->date_begin) && !empty($ex->date_end))–@endif{{ $ex->date_end ?? '' }}
                                            </div>
                                        @endif
                                        @if(!empty($ex->notes))
                                            <div class="met-ex-notes">{{ $ex->notes }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="met-empty-state">No exhibition history available.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function artGallery() {
    return {
        currentImage: '{{ $primaryImage ? asset('storage/' . $primaryImage->url) : '' }}',
        fullscreen: false,
        setImage(url) {
            this.currentImage = url;
        },
        openFullscreen() {
            if (this.currentImage) this.fullscreen = true;
        }
    }
}
</script>
@endpush

@endsection