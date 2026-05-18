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

    <!-- Artworks Table -->
    <section class="table-section">
        <h2 class="section-title">Artwork Collection</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Department</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($artworks ?? [] as $artwork)
                        <tr>
                            <td><strong>{{ $artwork['title'] ?? 'Untitled' }}</strong></td>
                            <td>{{ $artwork['artist'] ?? 'Unknown' }}</td>
                            <td>{{ $artwork['department'] ?? 'N/A' }}</td>
                            <td>{{ $artwork['date_created'] ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $artwork['status'] ?? 'active' }}">
                                    {{ ucfirst($artwork['status'] ?? 'active') }}
                                </span>
                            </td>
                            <td class="actions">
                                <button class="action-btn">View</button>
                                <button class="action-btn">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
.admin-page-section { max-width: 1200px; margin: 0 auto; }
.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem 0; }
.page-subtitle { font-size: 0.95rem; color: #666; margin: 0; }
.section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; }
.quick-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.table-section { background: white; border-radius: 8px; padding: 1.5rem; }
.table-wrapper { border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background-color: #f5f5f5; padding: 1rem; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e0e0e0; }
.data-table td { padding: 1rem; border-bottom: 1px solid #e0e0e0; }
.data-table tbody tr:hover { background-color: #f9f9f9; }
.status-badge { padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
.status-active { background-color: #d4edda; color: #155724; }
.status-inactive { background-color: #f8d7da; color: #721c24; }
.status-onloan { background-color: #d1ecf1; color: #0c5460; }
.actions { display: flex; gap: 0.5rem; }
.action-btn { padding: 0.4rem 0.8rem; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.8rem; transition: all 0.3s ease; }
.action-btn:hover { border-color: #2196F3; color: #2196F3; }
</style>
@endsection
