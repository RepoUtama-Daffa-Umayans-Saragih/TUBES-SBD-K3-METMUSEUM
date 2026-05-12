
@extends('admin.layouts.admin-layout')

@push('styles')
@vite('resources/css/admin/payments/index.css')
@endpush

@section('title', 'Payments')

@section('content')
<section class="admin-section admin-section--payments admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Payment Monitoring',
		'subtitle' => 'Review payment activity and status.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Payments', 'isCurrent' => true],
		],
		'status' => ['label' => 'Paid', 'tone' => 'paid'],
		'badges' => [
			['label' => 'Finance', 'tone' => 'neutral'],
			['label' => 'Refunds', 'tone' => 'warning'],
		],
		'filters' => ['Succeeded', 'Pending', 'Failed'],
		'actions' => [
			['label' => 'Review Refunds', 'variant' => 'secondary'],
			['label' => 'Export Payments', 'variant' => 'primary'],
		],
	])

	<div class="payments-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		<div class="payments-layout admin-grid admin-grid--main">
			<div class="payments-primary admin-stack">
				@include('admin.components.charts.payment-chart')
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Transaction Analytics</h3>
						<span class="admin-panel__meta">Placeholder insights</span>
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
			<div class="payments-side admin-stack">
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Refund Queue</h3>
						<span class="admin-panel__meta">Placeholder list</span>
					</div>
					<div class="admin-panel__body admin-list">
						<div class="admin-list__item">Pending refund placeholder</div>
						<div class="admin-list__item">Review request placeholder</div>
					</div>
				</div>
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Payment Status</h3>
						<span class="admin-panel__meta">Status indicators</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-pill-group">
							<span class="admin-pill">Succeeded</span>
							<span class="admin-pill">Pending</span>
							<span class="admin-pill">Failed</span>
						</div>
							<div class="admin-placeholder-block admin-placeholder-block--skeleton">
								<div class="skeleton skeleton--title"></div>
								<div class="skeleton skeleton--text"></div>
							</div>
					</div>
				</div>
			</div>
		</div>

		<div class="admin-panel">
			<div class="admin-panel__header">
				<h3 class="admin-panel__title">Payment Table</h3>
				<span class="admin-panel__meta">Transaction list</span>
			</div>
			@include('admin.components.tables.data-table')
		</div>

		<div class="admin-empty-state admin-empty-state--highlight">
			<div class="admin-empty-state__icon">
				<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
			</div>
			<div class="admin-empty-state__content">
				<h3 class="admin-empty-state__title">No payment activity</h3>
				<p class="admin-empty-state__text">Payments will appear here once available.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Record Payment</button>
		</div>
	</div>
</section>
@endsection
