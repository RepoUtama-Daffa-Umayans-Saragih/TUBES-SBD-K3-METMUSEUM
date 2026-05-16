
@extends('admin.layout.layout')

@push('styles')
@vite('resources/css/admin/analytics/index.css')
@endpush

@section('admin-title')
    Analytics
@endsection

@section('admin-content')
<section class="admin-section admin-section--analytics admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Analytics Dashboard',
		'subtitle' => 'Track revenue, visitors, and capacity trends.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Analytics', 'isCurrent' => true],
		],
		'status' => ['label' => 'Active', 'tone' => 'active'],
		'badges' => [
			['label' => 'Insights', 'tone' => 'neutral'],
			['label' => 'KPIs', 'tone' => 'info'],
		],
		'filters' => ['Monthly', 'Quarterly', 'Annual'],
		'actions' => [
			['label' => 'Download Snapshot', 'variant' => 'secondary'],
			['label' => 'Schedule Report', 'variant' => 'primary'],
		],
	])

	<div class="admin-analytics__section admin-stack">
		<div class="admin-analytics__header">
			<h2>Revenue Analytics</h2>
			<p>Revenue and payment overview placeholders.</p>
		</div>
		<div class="admin-grid admin-grid--two">
			@include('admin.components.charts.revenue-chart')
			@include('admin.components.charts.payment-chart')
		</div>
	</div>

	<div class="admin-analytics__section admin-stack">
		<div class="admin-analytics__header">
			<h2>Visitor Analytics</h2>
			<p>Traffic and ticket sales placeholders.</p>
		</div>
		<div class="admin-grid admin-grid--two">
			@include('admin.components.charts.visitor-chart')
			@include('admin.components.charts.ticket-sales-chart')
		</div>
	</div>

	<div class="admin-analytics__section admin-stack">
		<div class="admin-analytics__header">
			<h2>Exhibition Analytics</h2>
			<p>Exhibition engagement placeholders.</p>
		</div>
		<div class="admin-grid admin-grid--two">
			@include('admin.components.charts.exhibition-chart')
			@include('admin.components.charts.capacity-chart')
		</div>
	</div>

	<div class="admin-empty-state admin-empty-state--compact">
		<div class="admin-empty-state__icon">
			<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
		</div>
		<div class="admin-empty-state__content">
			<h3 class="admin-empty-state__title">No analytics data</h3>
			<p class="admin-empty-state__text">Analytics cards will populate when data is connected.</p>
		</div>
		<button class="admin-button admin-button--ghost" type="button">Refresh</button>
	</div>
</section>
@endsection
