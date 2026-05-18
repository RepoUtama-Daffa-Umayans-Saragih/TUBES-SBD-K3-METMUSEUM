@extends('admin.layout.layout')

@section('admin-title')
    Admin Dashboard
@endsection

@section('admin-content')
<div class="admin-dashboard">
    <!-- Top Section: Key Statistics -->
    <section class="analytics-section overview-section">
        <h2 class="section-title">Quick Overview</h2>
        <div class="analytics-cards-grid">
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Ticket Sales (Today)',
                'value' => $ticketsSoldToday ?? 0,
                'icon' => '🎫',
                'trend' => 'tickets',
                'color' => 'primary'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Total Revenue (Today)',
                'value' => '$' . number_format($totalRevenueToday ?? 0, 2),
                'icon' => '💰',
                'trend' => 'today',
                'color' => 'success'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Pending Orders',
                'value' => $pendingOrders ?? 0,
                'icon' => '📦',
                'trend' => 'orders',
                'color' => 'warning'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Pending Payments',
                'value' => $pendingPayments ?? 0,
                'icon' => '⏳',
                'trend' => 'payments',
                'color' => 'info'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Total Users',
                'value' => $totalUsers ?? 0,
                'icon' => '👥',
                'trend' => 'users',
                'color' => 'info'
            ])
            
            @include('admin.ticket-analytics.components.stat-card', [
                'title' => 'Total Artworks',
                'value' => $totalArtworks ?? 0,
                'icon' => '🎨',
                'trend' => 'artworks',
                'color' => 'primary'
            ])
        </div>
    </section>

    <!-- Dashboard Navigation Grid -->
    <section class="dashboard-grid">
        <h2 class="section-title">Quick Access</h2>
        <div class="dashboard-grid-container">
            <!-- Tickets Management Card -->
            <a href="{{ route('admin.tickets.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">🎫</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Tickets</h3>
                    <p class="dashboard-card__description">Manage ticket sales & stock</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Orders Management Card -->
            <a href="{{ route('admin.orders.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">📦</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Orders</h3>
                    <p class="dashboard-card__description">View & scan orders</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Payments Management Card -->
            <a href="{{ route('admin.payments.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">💳</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Payments</h3>
                    <p class="dashboard-card__description">Process & refund payments</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Analytics Card -->
            <a href="{{ route('admin.ticket-analytics.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">📈</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Analytics</h3>
                    <p class="dashboard-card__description">View detailed reports</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Artworks Management Card -->
            <a href="{{ route('admin.artworks.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">🎨</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Artworks</h3>
                    <p class="dashboard-card__description">Manage collection items</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Exhibitions Management Card -->
            <a href="{{ route('admin.exhibitions.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">🏛️</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Exhibitions</h3>
                    <p class="dashboard-card__description">Manage exhibitions</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>

            <!-- Users Management Card -->
            <a href="{{ route('admin.users.index') }}" class="dashboard-card">
                <div class="dashboard-card__icon">👥</div>
                <div class="dashboard-card__content">
                    <h3 class="dashboard-card__title">Users</h3>
                    <p class="dashboard-card__description">Manage system users</p>
                </div>
                <div class="dashboard-card__arrow">→</div>
            </a>


        </div>
    </section>
</div>

<style>
.admin-dashboard {
    max-width: 1400px;
    margin: 0 auto;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.analytics-section {
    margin-bottom: 3rem;
}

.analytics-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.dashboard-grid {
    margin-top: 3rem;
}

.dashboard-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.dashboard-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.dashboard-card:hover {
    border-color: #2196F3;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);
    transform: translateY(-2px);
}

.dashboard-card__icon {
    font-size: 2.5rem;
    flex-shrink: 0;
}

.dashboard-card__content {
    flex: 1;
    min-width: 0;
}

.dashboard-card__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: #333;
}

.dashboard-card__description {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
}

.dashboard-card__arrow {
    font-size: 1.5rem;
    color: #2196F3;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.dashboard-card:hover .dashboard-card__arrow {
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .analytics-cards-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    
    .dashboard-grid-container {
        grid-template-columns: 1fr;
    }
    
    .dashboard-card {
        padding: 1rem;
        gap: 1rem;
    }
    
    .dashboard-card__icon {
        font-size: 2rem;
    }
}
</style>
@endsection
