
@extends('admin.layout.layout')

@push('styles')
@vite('resources/css/admin/tickets/index.css')
@endpush

@section('admin-title')
    Tickets
@endsection

@section('admin-content')
<section class="admin-section admin-section--tickets admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Ticket Management',
		'subtitle' => 'Manage ticket types, quotas, and availability.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Tickets', 'isCurrent' => true],
		],
		'status' => ['label' => 'Active', 'tone' => 'active'],
		'badges' => [
			['label' => 'Inventory', 'tone' => 'neutral'],
			['label' => 'Quota', 'tone' => 'warning'],
		],
		'filters' => ['Today', 'This Week', 'All'],
		'actions' => [
			['label' => 'New Ticket Type', 'variant' => 'secondary'],
			['label' => 'Update Capacity', 'variant' => 'primary'],
		],
	])

	<div class="tickets-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		@include('admin.components.filters.filter-bar')

		<div class="tickets-layout admin-grid admin-grid--main">
			<div class="admin-panel tickets-table">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Ticket Inventory</h3>
					<span class="admin-panel__meta">Placeholder list</span>
				</div>
				@include('admin.components.tables.data-table')
			</div>
			<div class="tickets-side admin-stack">
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Availability</h3>
						<span class="admin-panel__meta">Quota overview</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-placeholder-block admin-placeholder-block--skeleton">
							<div class="skeleton skeleton--title"></div>
							<div class="skeleton skeleton--text"></div>
							<div class="skeleton skeleton--text"></div>
						</div>
						<div class="admin-pill-group">
							<span class="admin-pill">Open</span>
							<span class="admin-pill">Limited</span>
							<span class="admin-pill">Closed</span>
						</div>
					</div>
				</div>
				@include('admin.components.charts.capacity-chart')
			</div>
		</div>

		<div class="admin-empty-state admin-empty-state--highlight">
			<div class="admin-empty-state__icon">
				<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
			</div>
			<div class="admin-empty-state__content">
				<h3 class="admin-empty-state__title">No ticket updates</h3>
				<p class="admin-empty-state__text">New ticket changes will appear here.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Create Ticket</button>
		</div>
	</div>

	@include('admin.components.modals.base-modal')
</section>
@endsection
