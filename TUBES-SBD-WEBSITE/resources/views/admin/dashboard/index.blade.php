@extends('admin.layout.layout')

@push('styles')
@vite('resources/css/admin/dashboard/index.css')
@endpush

@section('admin-title')
    Dashboard
@endsection

@section('admin-content')
<section class="admin-dashboard admin-page">
    @include('admin.components.toolbar.page-toolbar', [
        'title' => 'Dashboard',
        'subtitle' => 'Analytics overview placeholder.',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'isCurrent' => true],
        ],
        'status' => ['label' => 'Active', 'tone' => 'active'],
        'badges' => [
            ['label' => 'Analytics', 'tone' => 'neutral'],
            ['label' => 'Today', 'tone' => 'info'],
        ],
        'filters' => ['Today', 'This Week', 'Monthly'],
        'actions' => [
            ['label' => 'Create Report', 'variant' => 'secondary'],
            ['label' => 'Add Quick Action', 'variant' => 'primary'],
        ],
    ])

    <div class="admin-dashboard__filters">
        @include('admin.components.filters.filter-bar')
    </div>

    <div class="admin-dashboard__summary admin-grid admin-grid--cards">
        @include('admin.components.cards.stat-card')
        @include('admin.components.cards.stat-card')
        @include('admin.components.cards.stat-card')
        @include('admin.components.cards.skeleton-card')
    </div>

    <div class="admin-dashboard__main admin-grid admin-grid--main">
        <div class="admin-dashboard__charts admin-grid admin-grid--charts">
            @include('admin.components.charts.revenue-chart')
            @include('admin.components.charts.visitor-chart')
            @include('admin.components.charts.ticket-sales-chart')
            @include('admin.components.charts.payment-chart')
        </div>
        <div class="admin-dashboard__side admin-stack">
            <div class="admin-panel">
                <div class="admin-panel__header">
                    <h3 class="admin-panel__title">Activity</h3>
                    <span class="admin-panel__meta">Last 24 hours</span>
                </div>
                <div class="admin-panel__body admin-list">
                    <div class="admin-list__item">Activity placeholder</div>
                    <div class="admin-list__item">System update placeholder</div>
                    <div class="admin-list__item">New ticket type placeholder</div>
                </div>
            </div>
            <div class="admin-panel">
                <div class="admin-panel__header">
                    <h3 class="admin-panel__title">Quick Actions</h3>
                </div>
                <div class="admin-panel__body">
                    @include('admin.components.toolbar.quick-actions')
                </div>
            </div>
        </div>
    </div>

    <div class="admin-dashboard__overview admin-grid admin-grid--two">
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">Operations Overview</h3>
                <span class="admin-panel__meta">Placeholder summary</span>
            </div>
            <div class="admin-panel__body">
                <div class="admin-placeholder-block admin-placeholder-block--skeleton">
                    <div class="skeleton skeleton--title"></div>
                    <div class="skeleton skeleton--text"></div>
                    <div class="skeleton skeleton--text"></div>
                </div>
            </div>
        </div>
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">Capacity Snapshot</h3>
                <span class="admin-panel__meta">Placeholder summary</span>
            </div>
            <div class="admin-panel__body">
                <div class="admin-placeholder-block admin-placeholder-block--skeleton">
                    <div class="skeleton skeleton--title"></div>
                    <div class="skeleton skeleton--text"></div>
                    <div class="skeleton skeleton--text"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-dashboard__table admin-stack">
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h3 class="admin-panel__title">Recent Transactions</h3>
                <span class="admin-panel__meta">Latest activity placeholder</span>
            </div>
            @include('admin.components.tables.data-table')
        </div>
        <div class="admin-empty-state admin-empty-state--compact">
            <div class="admin-empty-state__icon">
                <span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
            </div>
            <div class="admin-empty-state__content">
                <h3 class="admin-empty-state__title">No recent activity</h3>
                <p class="admin-empty-state__text">Transactions and updates will appear here.</p>
            </div>
            <button class="admin-button admin-button--ghost" type="button">Refresh</button>
        </div>
    </div>
</section>
@endsection
