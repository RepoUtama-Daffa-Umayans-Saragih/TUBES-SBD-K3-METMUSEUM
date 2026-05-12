
@extends('admin.layouts.admin-layout')

@push('styles')
@vite('resources/css/admin/orders/index.css')
@endpush

@section('title', 'Orders')

@section('content')
<section class="admin-section admin-section--orders admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Order Monitoring',
		'subtitle' => 'Track order flow and fulfillment status.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Orders', 'isCurrent' => true],
		],
		'status' => ['label' => 'Pending', 'tone' => 'pending'],
		'badges' => [
			['label' => 'Transactions', 'tone' => 'info'],
			['label' => 'Monitoring', 'tone' => 'neutral'],
		],
		'filters' => ['Pending', 'Paid', 'Expired'],
		'actions' => [
			['label' => 'Review Pending', 'variant' => 'secondary'],
			['label' => 'Export Orders', 'variant' => 'primary'],
		],
	])

	<div class="orders-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		@include('admin.components.filters.filter-bar')

		<div class="orders-layout admin-grid admin-grid--main">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Orders Table</h3>
					<span class="admin-panel__meta">Latest transactions</span>
				</div>
				@include('admin.components.tables.data-table')
			</div>
			<div class="orders-side admin-stack">
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Payment Status</h3>
						<span class="admin-panel__meta">Placeholder filter</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-pill-group">
							<span class="admin-pill">Pending</span>
							<span class="admin-pill">Paid</span>
							<span class="admin-pill">Expired</span>
						</div>
						<div class="admin-placeholder-block admin-placeholder-block--skeleton">
							<div class="skeleton skeleton--title"></div>
							<div class="skeleton skeleton--text"></div>
						</div>
					</div>
				</div>
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Transaction Overview</h3>
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
		</div>

		<div class="admin-empty-state admin-empty-state--compact">
			<div class="admin-empty-state__icon">
				<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
			</div>
			<div class="admin-empty-state__content">
				<h3 class="admin-empty-state__title">No orders yet</h3>
				<p class="admin-empty-state__text">Order activity will show up once available.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Create Order</button>
		</div>
	</div>
</section>
@endsection
