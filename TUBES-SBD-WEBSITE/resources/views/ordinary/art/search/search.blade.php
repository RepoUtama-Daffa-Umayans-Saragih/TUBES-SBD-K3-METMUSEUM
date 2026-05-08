@extends('layouts.app')

@section('title', 'Search for Art - The Met Collection')

@push('styles')
    @vite(['resources/css/ordinary/art/search/search.css'])
@endpush

@section('content')
<div class="search-art-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="{{ url('/') }}">🏠</a>
        <span>/</span>
        <a href="{{ route('art.index') }}">The Met Collection</a>
        <span>/</span>
        <span class="current">Search Art</span>
    </nav>

    <!-- Header -->
    <h1 class="page-title">Search for Art</h1>

    <!-- Search Bar -->
    <div class="search-bar-wrapper">
        <div class="search-bar">
            <!-- Field Dropdown -->
            <div class="field-dropdown">
                <button type="button" class="dropdown-toggle" id="fieldDropdownToggle">
                    <span id="selectedField">{{ $currentFieldLabel }}</span>
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="dropdown-menu" id="fieldDropdownMenu">
                    @php
                        $fields = [
                            'all' => 'All Fields',
                            'artist' => 'Artist / Culture',
                            'title' => 'Title',
                            'description' => 'Description',
                            'gallery' => 'Gallery',
                            'object_number' => 'Object Number',
                            'credit_line' => 'Credit Line'
                        ];
                    @endphp
                    @foreach($fields as $val => $label)
                        <a href="#" class="dropdown-item {{ ($currentField ?? 'all') === $val ? 'active' : '' }}" data-field="{{ $val }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Hidden field to track search category -->
            <input type="hidden" name="field" id="selectedFieldInput" value="{{ $currentField ?? 'all' }}">

            <!-- Search Input -->
            <input type="text" 
                   class="search-input" 
                   id="searchInput"
                   placeholder="Search all fields" 
                   value="{{ request('q', '') }}">
            
            <!-- Search Button -->
            <button type="button" class="search-btn" id="searchBtn">
                🔍
            </button>
        </div>

        <!-- Quick Filters -->
        <div class="quick-filters">
            <button type="button" class="filters-btn" id="filtersToggle">
                Filters
                <span class="filters-arrow">▼</span>
            </button>
            
            <div class="filter-checkboxes">
                <label class="checkbox-label">
                    <input type="checkbox" name="highlights" {{ request('highlights') ? 'checked' : '' }}>
                    <span>Highlights</span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="on_view" {{ request('on_view') ? 'checked' : '' }}>
                    <span>On view</span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="has_image" {{ request('has_image') ? 'checked' : '' }}>
                    <span>Has image</span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="open_access" {{ request('open_access') ? 'checked' : '' }}>
                    <span>Has Open Access image</span>
                    <span class="info-icon" title="Open Access images">ⓘ</span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="has_3d" {{ request('has_3d') ? 'checked' : '' }}>
                    <span>Has 3D image</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Sidebar + Results) -->
    <div class="search-content-layout" id="searchContentLayout">
        <!-- Advanced Filters Sidebar -->
        <div class="filters-panel" id="filtersPanel">
        <div class="filters-sidebar">
            <div class="filter-section expanded">
                <h3 class="filter-section-title">
                    Show only
                    <span class="section-arrow rotated">▼</span>
                </h3>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="highlights_adv" value="1">
                        <span>Highlights</span>
                        <span class="count">(2,804)</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="on_view_adv" value="1">
                        <span>On view</span>
                        <span class="count">(45,500)</span>
                    </label>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Images
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="has_image" value="1" {{ request('has_image') ? 'checked' : '' }}>
                        <span>Has image</span>
                        <span class="count">(369,695)</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="open_access" value="1" {{ request('open_access') ? 'checked' : '' }}>
                        <span>Has Open Access image</span>
                        <span class="count">(259,682)</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="has_3d" value="1" {{ request('has_3d') ? 'checked' : '' }}>
                        <span>Has 3D image</span>
                        <span class="count">(142)</span>
                    </label>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Date
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options">
                    <div class="date-filter-group">
                        <div class="date-input-wrapper">
                            <label>From</label>
                            <input type="text" name="year_start" class="date-input" placeholder="-400,000" value="{{ request('year_start') }}">
                        </div>
                        <div class="date-input-wrapper">
                            <label>To</label>
                            <input type="text" name="year_end" class="date-input" placeholder="Present" value="{{ request('year_end') }}">
                        </div>
                    </div>
                    <p class="date-helper-text">For BCE dates, enter a negative number. E.g. For 2000 BCE, enter -2000.</p>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Object type / material
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options scrollable-filter">
                    @foreach(['Prints', 'Books', 'Illustrations', 'Relief prints', 'Lithographs', 'Planographic prints', 'Woodcuts', 'Drawings', 'Paper', 'Engraving', 'Metal', 'Posters', 'Bark cloth', 'Metalwork', 'Silver', 'Color lithographs', 'Lithography', 'Offset lithography', 'Textiles', 'Offset lithographs', 'Costume', 'Photographs', 'Wood engravings', 'Paintings', 'Parchment', 'Sculpture', 'Vellum', 'Bamboo', 'Ceremonial masks', 'Gilt', 'Grass', 'Masks', 'Vessels', 'Jugs', 'Photomechanical reproductions', 'Printing blocks', 'Wood blocks', 'Albumen silver prints', 'Bone', 'Ephemera', 'Ink', 'Ivory', 'Photolithographs', 'Salvers', 'Wood', 'Card photographs', 'Cartes-de-visite', 'Etching', 'Photoengraving', 'Stipple engraving'] as $item)
                        <label class="filter-option">
                            <input type="checkbox" name="object_type[]" value="{{ $item }}" {{ is_array(request('object_type')) && in_array($item, request('object_type')) ? 'checked' : '' }}>
                            <span>{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Geographic location
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options scrollable-filter">
                    @foreach(['Africa', 'Akron', 'Asia', 'Audrain', 'Augsburg', 'Austria', 'Bavaria', 'Beijing', 'Boston', 'Bristol', 'Buffalo', 'Chicago', 'China', 'East New Britain', 'England', 'Europe', 'France', 'Germany', 'Illinois', 'Iran', 'London', 'Martinsburg', 'Massachusetts', 'Mexico', 'Missouri', 'New Britain', 'New Jersey', 'New York', 'Newark', 'North and Central America', 'Nuremberg', 'Oceania', 'Ohio', 'Papua New Guinea', 'Roman Empire', 'Scottish', 'United Kingdom', 'United States', 'Washington'] as $item)
                        <label class="filter-option">
                            <input type="checkbox" name="location[]" value="{{ $item }}" {{ is_array(request('location')) && in_array($item, request('location')) ? 'checked' : '' }}>
                            <span>{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Department
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options scrollable-filter">
                    @php
                        $departments = [
                            'African Art in The Michael C. Rockefeller Wing',
                            'The American Wing',
                            'Ancient American Art in The Michael C. Rockefeller Wing',
                            'Ancient West Asian Art',
                            'Arms and Armor',
                            'Asian Art',
                            'The Costume Institute',
                            'Drawings and Prints',
                            'Egyptian Art',
                            'European Paintings',
                            'European Sculpture and Decorative Arts',
                            'Greek and Roman Art',
                            'Islamic Art',
                            'Medieval Art and The Cloisters',
                            'The Michael C. Rockefeller Wing',
                            'Modern and Contemporary Art',
                            'Musical Instruments',
                            'Oceanic Art in The Michael C. Rockefeller Wing',
                            'Photographs',
                            'The Robert Lehman Collection',
                            'Thomas J. Watson Library'
                        ];
                    @endphp
                    @foreach($departments as $item)
                        <label class="filter-option">
                            <input type="checkbox" name="department[]" value="{{ $item }}" {{ is_array(request('department')) && in_array($item, request('department')) ? 'checked' : '' }}>
                            <span>{{ $item }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Geographic location
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options">
                    <p class="text-xs text-gray-500">Select locations...</p>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-section-title">
                    Department
                    <span class="section-arrow">▼</span>
                </h3>
                <div class="filter-options">
                    <p class="text-xs text-gray-500">Select departments...</p>
                </div>
            </div>
        </div>
        </div>
        
        <!-- Results Area -->
        <div class="results-area">
            <!-- Results Info -->
            <div class="results-info">
                <div class="results-count">
                    1-42 of {{ number_format($totalResults ?? 534724) }} results
                </div>
                
                <div class="sort-wrapper">
                    <label for="sortBy">Sort by:</label>
                    <select id="sortBy" class="sort-select">
                        <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="date_newest" {{ request('sort') == 'date_newest' ? 'selected' : '' }}>Date (Newest)</option>
                        <option value="date_oldest" {{ request('sort') == 'date_oldest' ? 'selected' : '' }}>Date (Oldest)</option>
                        <option value="artist" {{ request('sort') == 'artist' ? 'selected' : '' }}>Artist</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                    </select>
                </div>
            </div>

            <!-- Art Grid -->
            <div class="art-grid">
        @forelse($artworks ?? [] as $artwork)
            <a href="{{ route('artwork.show', $artwork->slug) }}" class="art-card">
                <div class="art-image-wrapper">
                    <img src="{{ $artwork->image_url ?? asset('images/placeholder.jpg') }}" 
                         alt="{{ $artwork->title }}"
                         class="art-image">
                </div>
                <div class="art-info">
                    <h3 class="art-title">{{ $artwork->title }}</h3>
                    <p class="art-artist">
                        {{ $artwork->constituents->first()->display_name ?? ($artwork->cultures->first()->culture_name ?? 'Unknown') }}
                    </p>
                    <p class="art-date">{{ $artwork->object_date_display ?? 'Unknown' }}</p>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <h3>No artworks found</h3>
                <p>Try adjusting your search or filters to find what you're looking for.</p>
            </div>
        @endforelse
    </div>

            <!-- Pagination -->
            @if(isset($artworks) && $artworks->hasPages())
                <div class="pagination">
                    {{ $artworks->links() }}
                </div>
            @endif
        </div> <!-- End Results Area -->
    </div> <!-- End Main Content Layout -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Field Dropdown Toggle
    const fieldDropdownToggle = document.getElementById('fieldDropdownToggle');
    const fieldDropdownMenu = document.getElementById('fieldDropdownMenu');
    const selectedField = document.getElementById('selectedField');
    const searchInput = document.getElementById('searchInput');
    
    fieldDropdownToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        fieldDropdownMenu.classList.toggle('show');
        this.classList.toggle('active');
    });

    // Field Selection
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const selectedFieldInput = document.getElementById('selectedFieldInput');
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const field = this.dataset.field;
            const text = this.textContent;
            
            // Update UI
            selectedField.textContent = text;
            searchInput.placeholder = `Search ${text.toLowerCase()}`;
            
            // Update Hidden Input
            if (selectedFieldInput) {
                selectedFieldInput.value = field;
            }
            
            // Update Active State
            dropdownItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Close Menu
            fieldDropdownMenu.classList.remove('show');
            fieldDropdownToggle.classList.remove('active');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!fieldDropdownToggle.contains(e.target) && !fieldDropdownMenu.contains(e.target)) {
            fieldDropdownMenu.classList.remove('show');
            fieldDropdownToggle.classList.remove('active');
        }
    });

    // Filters Panel Toggle
    const filtersToggle = document.getElementById('filtersToggle');
    const filtersPanel = document.getElementById('filtersPanel');
    const filtersArrow = document.querySelector('.filters-arrow');
    
    filtersToggle.addEventListener('click', function() {
        filtersPanel.classList.toggle('show');
        filtersArrow.classList.toggle('rotated');
        this.classList.toggle('active');
    });

    // Filter Section Toggle (untuk accordion)
    const sectionTitles = document.querySelectorAll('.filter-section-title');
    sectionTitles.forEach(title => {
        title.addEventListener('click', function() {
            const section = this.parentElement;
            const arrow = this.querySelector('.section-arrow');
            
            section.classList.toggle('expanded');
            if (arrow) {
                arrow.classList.toggle('rotated');
            }
        });
    });

    // Search Form Submission
    const searchBtn = document.getElementById('searchBtn');
    const searchForm = document.querySelector('.search-bar');
    
    searchBtn.addEventListener('click', function() {
        performSearch();
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    function performSearch() {
        const query = searchInput.value.trim();
        const field = selectedFieldInput ? selectedFieldInput.value : 'all';
        
        const url = new URL(window.location.href);
        
        // Update Query
        if (query) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }
        
        // Update Field
        if (field && field !== 'all') {
            url.searchParams.set('field', field);
        } else {
            url.searchParams.delete('field');
        }
        
        // Reset pagination on new search
        url.searchParams.delete('page');
        
        window.location.href = url.toString();
    }

    // Sort Change Handler
    const sortBy = document.getElementById('sortBy');
    sortBy.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
});
</script>
@endpush