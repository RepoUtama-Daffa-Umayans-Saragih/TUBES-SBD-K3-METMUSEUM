@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/art/art.css')
@endpush

@section('content')
<div class="art-page">
    <!-- Header Section -->
    <div class="art-header">
        <h1>Explore Our Collections</h1>
        <p class="subtitle">Discover thousands of artworks from around the world</p>
    </div>

    <!-- Search & Filter Section -->
    <div class="art-search-section">
        <form action="{{ route('art.search') }}" method="GET" class="art-filter-form">
            <!-- Search Input -->
            <div class="filter-row">
                <div class="filter-group search-group">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search by title or description..."
                        class="search-input"
                        value="{{ request('q') ?? '' }}"
                    >
                    <button type="submit" class="btn btn-primary">
                        🔍 Search
                    </button>
                </div>
            </div>

            <!-- Filter Dropdowns -->
            <div class="filter-row">
                <div class="filter-group">
                    <label for="department_filter">Department</label>
                    <select name="department_id" id="department_filter" class="filter-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ request('department_id') == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="artist_filter">Artist</label>
                    <select name="artist_id" id="artist_filter" class="filter-select">
                        <option value="">All Artists</option>
                        @foreach($artists as $artist)
                            <option value="{{ $artist->artist_id }}" {{ request('artist_id') == $artist->artist_id ? 'selected' : '' }}>
                                {{ $artist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="type_filter">Object Type</label>
                    <select name="type_id" id="type_filter" class="filter-select">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->type_id }}" {{ request('type_id') == $type->type_id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="geo_filter">Geo Location</label>
                    <select name="geo_id" id="geo_filter" class="filter-select">
                        <option value="">All Locations</option>
                        @foreach($geolocations as $geo)
                            <option value="{{ $geo->geo_id }}" {{ request('geo_id') == $geo->geo_id ? 'selected' : '' }}>
                                {{ $geo->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            @if(request('q') || request('department_id') || request('artist_id') || request('type_id') || request('geo_id'))
                <div class="active-filters">
                    @if(request('q'))
                        <span class="filter-tag">
                            Search: <strong>{{ request('q') }}</strong>
                            <a href="{{ url()->current() }}?{{ http_build_query(array_filter(array_merge(request()->query(), ['q' => null]))) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if(request('department_id'))
                        <span class="filter-tag">
                            Department: <strong>{{ optional($departments->find(request('department_id'), 'department_id'))->name ?? 'Unknown' }}</strong>
                            <a href="{{ url()->current() }}?{{ http_build_query(array_filter(array_merge(request()->query(), ['department_id' => null]))) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    @if(request('artist_id'))
                        <span class="filter-tag">
                            Artist: <strong>{{ optional($artists->find(request('artist_id'), 'artist_id'))->name ?? 'Unknown' }}</strong>
                            <a href="{{ url()->current() }}?{{ http_build_query(array_filter(array_merge(request()->query(), ['artist_id' => null]))) }}" class="remove-filter">×</a>
                        </span>
                    @endif
                    <a href="{{ route('art.index') }}" class="clear-filters">Clear all filters</a>
                </div>
            @endif
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        @if($artworks->count() > 0)
            <p>Showing <strong>{{ $artworks->count() }}</strong> of <strong>{{ $artworks->total() }}</strong> artworks</p>
        @endif
    </div>

    <!-- Artworks Grid Section -->
    @if($artworks->count() > 0)
        <div class="artworks-grid">
            @foreach($artworks as $artwork)
                <div class="artwork-item">
                    <a href="{{ route('art.show', $artwork->art_work_id) }}" class="artwork-card">
                        <div class="artwork-image">
                            <img
                                src="{{ $artwork->images->firstWhere('is_primary', true)->url ?? 'https://via.placeholder.com/280x320?text=No+Image' }}"
                                alt="{{ $artwork->title }}"
                                loading="lazy"
                            >
                            <div class="artwork-overlay">
                                <span class="view-details">View Details</span>
                            </div>
                        </div>

                        <div class="artwork-info">
                            <h3 class="artwork-title">{{ $artwork->title }}</h3>
                            @if($artwork->artists->count() > 0)
                                <p class="artwork-artist">{{ $artwork->artists->pluck('name')->join(', ') }}</p>
                            @endif
                            @if($artwork->year_start || $artwork->year_end)
                                <p class="artwork-date">
                                    {{ $artwork->year_start }}
                                    @if($artwork->year_end && $artwork->year_end != $artwork->year_start)
                                        - {{ $artwork->year_end }}
                                    @endif
                                </p>
                            @endif
                            @if($artwork->department)
                                <span class="artwork-department">{{ $artwork->department->name }}</span>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $artworks->links('pagination::bootstrap-4') }}
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <h2>No Artworks Found</h2>
            <p>Try adjusting your search keywords or filters.</p>
            <a href="{{ route('art.index') }}" class="btn btn-primary">View All Collections</a>
        </div>
    @endif
</div>

@endsection

@endsection
