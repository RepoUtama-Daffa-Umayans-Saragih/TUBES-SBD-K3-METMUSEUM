
@extends('admin.layout.layout')

@section('admin-title')
    User Management
@endsection

@section('admin-content')
<div class="admin-page-section">
    <div class="page-header">
        <h1>User Management</h1>
        <p class="page-subtitle">Manage system users and permissions</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Total Users',
            'value' => $totalUsers ?? 0,
            'icon' => '👥',
            'trend' => 'registered',
            'color' => 'primary'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Admins',
            'value' => $adminCount ?? 0,
            'icon' => '🔐',
            'trend' => 'active',
            'color' => 'warning'
        ])
        
        @include('admin.ticket-analytics.components.stat-card', [
            'title' => 'Active Today',
            'value' => $activeToday ?? 0,
            'icon' => '✓',
            'trend' => 'online',
            'color' => 'success'
        ])
    </div>

    <!-- Users Table -->
    <section class="table-section">
        <h2 class="section-title">Users Directory</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users ?? [] as $user)
                        <tr>
                            <td><strong>{{ $user['id'] ?? 'N/A' }}</strong></td>
                            <td>{{ $user['name'] ?? 'N/A' }}</td>
                            <td>{{ $user['email'] ?? 'N/A' }}</td>
                            <td>{{ ucfirst($user['role'] ?? 'user') }}</td>
                            <td>
                                <span class="status-badge status-{{ $user['status'] ?? 'active' }}">
                                    {{ ucfirst($user['status'] ?? 'active') }}
                                </span>
                            </td>
                            <td>{{ $user['created_at'] ?? 'N/A' }}</td>
                            <td class="actions">
                                <button class="action-btn">Edit</button>
                                <button class="action-btn action-danger">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
.admin-page-section {
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #666;
    margin: 0;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.table-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
}

.table-wrapper {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #f5f5f5;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.data-table tbody tr:hover {
    background-color: #f9f9f9;
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    padding: 0.4rem 0.8rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.action-btn:hover {
    border-color: #2196F3;
    color: #2196F3;
}

.action-danger {
    color: #f44336;
}

.action-danger:hover {
    border-color: #f44336;
}
</style>
@endsection
