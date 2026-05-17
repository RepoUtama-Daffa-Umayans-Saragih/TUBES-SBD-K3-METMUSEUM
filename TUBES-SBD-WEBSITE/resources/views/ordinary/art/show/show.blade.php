@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/show/show.css')
@endpush

@section('content')

@php
    /* ── Images ── */
    $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
    $allImages    = $artwork->images ?? collect();

    /* ── Constituents ── */
    $allConstituents = $artwork->constituents ?? collect();
    $makers          = $allConstituents->where('role', 'Maker');
    $artists         = $allConstituents->where('role', 'Artist');
    $displayArtists  = $artists->isNotEmpty() ? $artists : ($makers->isNotEmpty() ? $makers : $allConstituents);

    /* ── Date ── */
    $dateStr = '';
    if (!empty($artwork->object_date)) {
        $dateStr = $artwork->object_date;
    } elseif (!empty($artwork->year_start) && !empty($artwork->year_end) && $artwork->year_start !== $artwork->year_end) {
        $dateStr = $artwork->year_start . '–' . $artwork->year_end;
    } elseif (!empty($artwork->year_start)) {
        $dateStr = (string) $artwork->year_start;
    } elseif (!empty($artwork->year_end)) {
        $dateStr = (string) $artwork->year_end;
    }

    /* ── Gallery ── */
    $galleryText = '';
    if (!empty($artwork->location)) {
        $galleryText = $artwork->location;
        if (!empty($artwork->gallery_number)) $galleryText .= ', Gallery ' . $artwork->gallery_number;
    }

    /* ── Metadata ── */
    $cultures        = ($artwork->cultures ?? collect())->pluck('name')->join(', ');
    $mediumName      = $artwork->medium?->name ?? '';
    $classificationN = $artwork->classification?->name ?? '';
    $departmentName  = $artwork->department?->name ?? '';
    $creditLine      = $artwork->creditLine?->description ?? $artwork->credit_line ?? '';
    $objectNumber    = $artwork->accession_number ?? '';

    /* ── Dimensions ── */
    $dimensions = '';
    $meas = $artwork->measurements ?? collect();
    if ($meas->isNotEmpty()) {
        $dimensions = $meas->map(fn($m) =>
            trim(($m->element_name ? $m->element_name . ': ' : '') . ($m->element_description ?? ''))
        )->filter()->join('; ');
    }

    /* ── SIMs ── */
    $sims         = $artwork->sims ?? collect();
    $signatures   = $sims->where('type', 'Signature');
    $inscriptions = $sims->where('type', 'Inscription');
    $markings     = $sims->where('type', 'Marking');
    $hasSims      = $signatures->isNotEmpty() || $inscriptions->isNotEmpty() || $markings->isNotEmpty();

    /* ── Exhibitions ── */
    $exhibitions = $artwork->exhibitionHistories ?? collect();

    /* ── Related artworks ── */
    $related = collect();
    try {
        $related = \App\Models\ArtWork::where('id', '!=', $artwork->id)
            ->where(function($q) use ($artwork) {
                if ($artwork->department_id)     $q->orWhere('department_id', $artwork->department_id);
                if ($artwork->classification_id) $q->orWhere('classification_id', $artwork->classification_id);
            })
            ->with(['images'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
    } catch(\Exception $e) {}
@endphp

<div class="met-page">

    {{-- ═══════════ BREADCRUMB ═══════════ --}}
    <div class="met-breadcrumb-bar">
        <div class="met-wrap">
            <nav class="met-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('art.index') }}">Collection</a>
                <span>/</span>
                <span>{{ Str::limit($artwork->title, 80) }}</span>
            </nav>
        </div>
    </div>

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="met-hero">
        <div class="met-wrap met-hero__grid">

            {{-- LEFT: Info --}}
            <div class="met-hero__info">

                <h1 class="met-title">{{ $artwork->title }}</h1>

                {{-- Artists / Makers --}}
                <div class="met-artist-block">
                    @forelse($displayArtists as $c)
                        <div class="met-artist-line">
                            @if(!empty($c->role) && !in_array($c->role, ['Artist']))
                                <span class="met-role">{{ $c->role }}:</span>
                            @endif
                            <span class="met-artist-name">{{ $c->name }}</span>
                            @if(!empty($c->nationality))
                                <span class="met-artist-nat">{{ $c->nationality }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="met-artist-line"><span class="met-artist-name">Unknown Artist</span></div>
                    @endforelse
                </div>

                {{-- Quick metadata --}}
                <div class="met-quickmeta">
                    @if($dateStr)
                        <div class="met-qm-row">
                            <span class="met-qm-label">Date</span>
                            <span class="met-qm-val">{{ $dateStr }}</span>
                        </div>
                    @endif
                    @if($cultures)
                        <div class="met-qm-row">
                            <span class="met-qm-label">Culture</span>
                            <span class="met-qm-val">{{ $cultures }}</span>
                        </div>
                    @endif
                    @if($mediumName)
                        <div class="met-qm-row">
                            <span class="met-qm-label">Medium</span>
                            <span class="met-qm-val">{{ $mediumName }}</span>
                        </div>
                    @endif
                    @if($departmentName)
                        <div class="met-qm-row">
                            <span class="met-qm-label">Department</span>
                            <span class="met-qm-val">{{ $departmentName }}</span>
                        </div>
                    @endif
                    @if($galleryText)
                        <div class="met-qm-row">
                            <span class="met-qm-icon">&#9679;</span>
                            <span class="met-qm-val met-qm-val--gallery">On view at <strong>{{ $galleryText }}</strong></span>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if(!empty($artwork->description))
                    <div class="met-desc" x-data="{ open: false }">
                        <div class="met-desc__body" :class="open ? 'met-desc__body--open' : ''">
                            {!! nl2br(e($artwork->description)) !!}
                        </div>
                        <button class="met-desc__toggle"
                            @click="open = !open"
                            x-text="open ? 'View less ↑' : 'View more ↓'">View more ↓</button>
                    </div>
                @endif

                {{-- Action row --}}
                <div class="met-hero-actions">
                    <button class="met-btn"
                        onclick="navigator.share ? navigator.share({title:'{{ addslashes($artwork->title) }}',url:window.location.href}) : (navigator.clipboard.writeText(window.location.href), alert('Link copied!'))">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        Share
                    </button>
                    <a href="{{ route('art.index') }}" class="met-btn met-btn--ghost">← Back to Collection</a>
                </div>

            </div>{{-- /hero__info --}}

            {{-- RIGHT: Gallery --}}
            <div class="met-hero__gallery" x-data="metGallery('{{ $primaryImage ? asset('storage/' . $primaryImage->url) : '' }}')">

                <div class="met-imgframe" @click="openLightbox()">
                    @if($primaryImage)
                        <img :src="current" alt="{{ $artwork->title }}" class="met-mainimg">
                        <span class="met-imgframe__zoom">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                        </span>
                    @else
                        <div class="met-no-img">No image available</div>
                    @endif
                </div>

                {{-- Bar --}}
                <div class="met-imgbar">
                    <div>
                        @if($artwork->is_public_domain)
                            <span class="met-pd">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path fill="white" d="M9 12l2 2 4-4"/></svg>
                                Public Domain
                            </span>
                        @endif
                    </div>
                    <div class="met-imgbar__actions">
                        <button class="met-icon-btn" title="Download" @click.stop="downloadImage()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        </button>
                        <button class="met-icon-btn" title="Share"
                            onclick="navigator.share ? navigator.share({url:window.location.href}) : navigator.clipboard.writeText(window.location.href)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </button>
                        <button class="met-icon-btn" title="Fullscreen" @click.stop="openLightbox()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Thumbnails --}}
                @if($allImages->count() > 1)
                    <div class="met-thumbs">
                        @foreach($allImages as $img)
                            @php $u = asset('storage/' . $img->url); @endphp
                            <button class="met-thumb"
                                :class="current === '{{ $u }}' ? 'met-thumb--on' : ''"
                                @click="current='{{ $u }}'" type="button">
                                <img src="{{ $u }}" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Lightbox --}}
                <div class="met-lightbox" x-show="lb" x-cloak @keydown.escape.window="lb=false" @click="lb=false" style="display:none">
                    <button class="met-lightbox__close" @click.stop="lb=false">✕</button>
                    <img :src="current" class="met-lightbox__img" @click.stop>
                </div>

            </div>{{-- /hero__gallery --}}

        </div>
    </section>

    {{-- ═══════════ ARTWORK DETAILS ═══════════ --}}
    <section class="met-details" x-data="{ tab: 'overview' }">
        <div class="met-wrap">

            <h2 class="met-section-title">Artwork Details</h2>

            <div class="met-details__layout">

                {{-- Sidebar --}}
                <nav class="met-sidenav">
                    @foreach([
                        ['overview',   'Overview'],
                        ['sims',       'Signatures, Inscriptions, and Markings'],
                        ['provenance', 'Provenance'],
                        ['exhibition', 'Exhibition History'],
                    ] as [$id, $label])
                        <button class="met-sidenav__btn"
                            :class="tab === '{{ $id }}' ? 'met-sidenav__btn--active' : ''"
                            @click="tab = '{{ $id }}'"
                            type="button">{{ $label }}</button>
                    @endforeach
                </nav>

                {{-- Content --}}
                <div class="met-panel">

                    {{-- Overview --}}
                    <div x-show="tab==='overview'" x-transition.opacity.duration.200ms>
                        <dl class="met-dl">
                            @php
                                $rows = [
                                    'Title'                 => $artwork->title,
                                    'Maker'                 => $makers->isNotEmpty()
                                        ? $makers->map(fn($m)=>$m->name.($m->nationality?' ('.$m->nationality.')':''))->join("\n") : null,
                                    'Artist'                => $artists->isNotEmpty()
                                        ? $artists->map(fn($a)=>$a->name.($a->nationality?' ('.$a->nationality.')':''))->join("\n") : null,
                                    'Date'                  => $dateStr ?: null,
                                    'Culture'               => $cultures ?: null,
                                    'Medium'                => $mediumName ?: null,
                                    'Dimensions'            => $dimensions ?: null,
                                    'Classification'        => $classificationN ?: null,
                                    'Credit Line'           => $creditLine ?: null,
                                    'Object Number'         => $objectNumber ?: null,
                                    'Curatorial Department' => $departmentName ?: null,
                                ];
                            @endphp
                            @foreach($rows as $label => $value)
                                @if($value)
                                    <div class="met-dl__row">
                                        <dt class="met-dl__dt">{{ $label }}:</dt>
                                        <dd class="met-dl__dd">
                                            @foreach(explode("\n", $value) as $ln)
                                                {{ $ln }}<br>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>

                    {{-- SIMs --}}
                    <div x-show="tab==='sims'" x-transition.opacity.duration.200ms>
                        @if($hasSims)
                            <dl class="met-dl">
                                @foreach($signatures as $s)
                                    <div class="met-dl__row">
                                        <dt class="met-dl__dt">Signature:</dt>
                                        <dd class="met-dl__dd">{{ $s->description }}</dd>
                                    </div>
                                @endforeach
                                @foreach($inscriptions as $s)
                                    <div class="met-dl__row">
                                        <dt class="met-dl__dt">Inscription:</dt>
                                        <dd class="met-dl__dd">{{ $s->description }}</dd>
                                    </div>
                                @endforeach
                                @foreach($markings as $s)
                                    <div class="met-dl__row">
                                        <dt class="met-dl__dt">Marking:</dt>
                                        <dd class="met-dl__dd">{{ $s->description }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        @else
                            <p class="met-empty">No signatures, inscriptions, or markings available.</p>
                        @endif
                    </div>

                    {{-- Provenance --}}
                    <div x-show="tab==='provenance'" x-transition.opacity.duration.200ms>
                        @if(!empty($artwork->provenance))
                            <div class="met-prose">{{ $artwork->provenance }}</div>
                        @else
                            <p class="met-empty">No provenance information available.</p>
                        @endif
                    </div>

                    {{-- Exhibitions --}}
                    <div x-show="tab==='exhibition'" x-transition.opacity.duration.200ms>
                        @if($exhibitions->isNotEmpty())
                            <ul class="met-exlist">
                                @foreach($exhibitions as $ex)
                                    <li class="met-exitem">
                                        <strong class="met-exitem__title">{{ $ex->title ?? $ex->exhibition_title ?? '—' }}</strong>
                                        @if(!empty($ex->venue))
                                            <span class="met-exitem__venue">{{ $ex->venue }}@if(!empty($ex->city)), {{ $ex->city }}@endif</span>
                                        @endif
                                        @if(!empty($ex->date_begin) || !empty($ex->date_end))
                                            <span class="met-exitem__dates">{{ $ex->date_begin ?? '' }}@if(!empty($ex->date_begin)&&!empty($ex->date_end))–@endif{{ $ex->date_end ?? '' }}</span>
                                        @endif
                                        @if(!empty($ex->notes))
                                            <span class="met-exitem__notes">{{ $ex->notes }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="met-empty">No exhibition history available.</p>
                        @endif
                    </div>

                </div>{{-- /panel --}}
            </div>
        </div>
    </section>

    {{-- ═══════════ MORE ARTWORK ═══════════ --}}
    @if($related->isNotEmpty())
        <section class="met-more">
            <div class="met-wrap">
                <h2 class="met-section-title">More Artwork</h2>
                <div class="met-more-grid">
                    @foreach($related as $rel)
                        @php $ri = $rel->images->firstWhere('is_primary',true) ?? $rel->images->first(); @endphp
                        <a href="{{ route('art.show', $rel->id) }}" class="met-card">
                            <div class="met-card__img">
                                @if($ri)
                                    <img src="{{ asset('storage/'.$ri->url) }}" alt="{{ $rel->title }}" loading="lazy">
                                @else
                                    <div class="met-card__noimg">No image</div>
                                @endif
                            </div>
                            <div class="met-card__body">
                                <p class="met-card__title">{{ $rel->title }}</p>
                                @if($rel->department)
                                    <p class="met-card__dept">{{ $rel->department->name }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</div>{{-- .met-page --}}

@push('scripts')
<script>
function metGallery(initial) {
    return {
        current: initial,
        lb: false,
        openLightbox() { if (this.current) this.lb = true; },
        downloadImage() {
            if (!this.current) return;
            const a = document.createElement('a');
            a.href = this.current; a.download = ''; a.target = '_blank'; a.click();
        }
    };
}
</script>
@endpush

@endsection