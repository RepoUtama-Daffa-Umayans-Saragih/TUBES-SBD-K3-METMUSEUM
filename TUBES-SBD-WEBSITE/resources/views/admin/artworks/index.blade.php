@extends('admin.layout.layout')

@section('admin-title')
    Artwork Management
@endsection

@section('admin-content')
<div class="admin-page-section">
    <div class="page-header">
        <h1>Artwork Management</h1>
        <p class="page-subtitle">Manage museum collection and artwork catalog</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Total Artworks',
            'value' => $totalArtworks ?? 0,
            'icon' => '🎨',
            'trend' => 'in collection',
            'color' => 'primary'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Departments',
            'value' => $totalDepartments ?? 0,
            'icon' => '🏛️',
            'trend' => 'categories',
            'color' => 'info'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'On Display',
            'value' => $onDisplay ?? 0,
            'icon' => '✓',
            'trend' => 'visible',
            'color' => 'success'
        ])
    </div>

    <!-- Artworks Grid -->
    <section class="table-section">
        <h2 class="section-title">Artwork Collection</h2>
        
        @forelse($artworks as $artwork)
            @if($loop->first)
                <div class="artworks-grid">
            @endif
            
            <!-- Artwork Card -->
            <div class="artwork-card">
                <!-- Image Container -->
                <div class="artwork-image-container">
                    @php
                        $primaryImage = $artwork->images->firstWhere('is_primary', true) ?? $artwork->images->first();
                        $imageUrl = $primaryImage?->image_url;
                    @endphp
                    
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $artwork->title }}" class="artwork-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="artwork-image-fallback" style="display: none;">
                            <span>📷</span>
                            <p>Image unavailable</p>
                        </div>
                    @else
                        <div class="artwork-image-fallback">
                            <span>📷</span>
                            <p>No image</p>
                        </div>
                    @endif
                </div>
                
                <!-- Card Info -->
                <div class="artwork-card-info">
                    <!-- Title -->
                    <h3 class="artwork-title">{{ $artwork->title ?? 'Untitled' }}</h3>
                    
                    <!-- Accession Number -->
                    @if($artwork->accession_number)
                        <p class="artwork-accession">{{ $artwork->accession_number }}</p>
                    @endif
                    
                    <!-- Artist -->
                    <p class="artwork-artist">
                        <strong>Artist:</strong>
                        @if($artwork->constituents->isNotEmpty())
                            {{ $artwork->constituents->pluck('display_name')->join(', ') }}
                        @else
                            <span style="color: #999;">Unknown</span>
                        @endif
                    </p>
                    
                    <!-- Department -->
                    <p class="artwork-department">
                        <strong>Department:</strong> {{ $artwork->department?->department_name ?? 'N/A' }}
                    </p>
                    
                    <!-- Date -->
                    <p class="artwork-date">
                        <strong>Date:</strong>
                        @if($artwork->object_date_display)
                            {{ $artwork->object_date_display }}
                        @elseif($artwork->object_begin_date)
                            {{ $artwork->object_begin_date }}
                        @else
                            <span style="color: #999;">N/A</span>
                        @endif
                    </p>
                    
                    <!-- Status Badge -->
                    <div class="artwork-status">
                        @if($artwork->is_on_view)
                            <span class="status-badge status-active">✓ On View</span>
                        @else
                            <span class="status-badge status-inactive">Not Displayed</span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($loop->last)
                </div>
            @endif
        @empty
            <div style="text-align: center; padding: 3rem; color: #999;">
                <p style="font-size: 2rem; margin-bottom: 1rem;">📭</p>
                <p>No artworks found in the collection yet.</p>
                <small>Start by adding artwork records to the system.</small>
            </div>
        @endforelse
        
        <!-- Pagination -->
        @if($artworks->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.85rem; color: #666;">
                    Showing {{ $artworks->firstItem() }} to {{ $artworks->lastItem() }} of {{ $artworks->total() }} artworks
                </div>
                <div>{{ $artworks->links() }}</div>
            </div>
        @endif
    </section>
</div>

<style>
.admin-page-section { max-width: 1400px; margin: 0 auto; }
.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem 0; }
.page-subtitle { font-size: 0.95rem; color: #666; margin: 0; }
.section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; }

/* Quick Stats Grid */
.quick-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem; }

/* Artwork Grid */
.table-section { background: white; border-radius: 8px; padding: 1.5rem; }

.artworks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Artwork Card */
.artwork-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.artwork-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #2196F3;
}

/* Image Container */
.artwork-image-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    background-color: #f5f5f5;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.artwork-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: #f5f5f5;
}

.artwork-image-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f5f5f5, #e8e8e8);
    color: #999;
}

.artwork-image-fallback span {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.artwork-image-fallback p {
    margin: 0;
    font-size: 0.9rem;
}

/* Card Info */
.artwork-card-info {
    padding: 1.2rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.artwork-title {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    line-height: 1.3;
    word-break: break-word;
}

.artwork-accession {
    margin: 0 0 0.8rem 0;
    font-size: 0.8rem;
    color: #999;
    font-weight: 500;
}

.artwork-artist,
.artwork-department,
.artwork-date {
    margin: 0.4rem 0;
    font-size: 0.85rem;
    color: #555;
    line-height: 1.4;
}

.artwork-artist strong,
.artwork-department strong,
.artwork-date strong {
    color: #333;
    font-weight: 600;
}

.artwork-status {
    margin-top: auto;
    padding-top: 0.8rem;
    border-top: 1px solid #e0e0e0;
}

/* Status Badges */
.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

/* Responsive */
@media (max-width: 768px) {
    .artworks-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }
    
    .artwork-card-info {
        padding: 1rem;
    }
    
    .artwork-title {
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .artworks-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
