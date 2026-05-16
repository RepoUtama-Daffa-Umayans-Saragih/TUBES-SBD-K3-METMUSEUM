
@extends('admin.layout.layout')

@push('styles')
@vite('resources/css/admin/exhibitions/index.css')
@endpush

@section('admin-title')
    Exhibitions
@endsection

@section('admin-content')
<section class="admin-section admin-section--exhibitions admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Exhibition Management',
		'subtitle' => 'Plan exhibitions and track performance.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Exhibitions', 'isCurrent' => true],
		],
		'status' => ['label' => 'Draft', 'tone' => 'draft'],
		'badges' => [
			['label' => 'Schedule', 'tone' => 'neutral'],
			['label' => 'Planning', 'tone' => 'info'],
		],
		'filters' => ['Current', 'Upcoming', 'Archive'],
		'actions' => [
			['label' => 'Add Exhibition', 'variant' => 'secondary'],
			['label' => 'Publish Schedule', 'variant' => 'primary'],
		],
	])

	<div class="exhibitions-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		<div class="exhibitions-layout admin-grid admin-grid--main">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Exhibition Schedule</h3>
					<span class="admin-panel__meta">Timeline placeholder</span>
				</div>
				<div class="admin-panel__body admin-list">
					<div class="admin-list__item">Upcoming exhibition placeholder</div>
					<div class="admin-list__item">Current exhibition placeholder</div>
					<div class="admin-list__item">Planning placeholder</div>
				</div>
			</div>
			<div class="exhibitions-side admin-stack">
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Timeline</h3>
						<span class="admin-panel__meta">Placeholder view</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-placeholder-block admin-placeholder-block--skeleton">
							<div class="skeleton skeleton--title"></div>
							<div class="skeleton skeleton--text"></div>
						</div>
					</div>
				</div>
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Popularity</h3>
						<span class="admin-panel__meta">Placeholder metrics</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-placeholder-block admin-placeholder-block--skeleton">
							<div class="skeleton skeleton--title"></div>
							<div class="skeleton skeleton--text"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="exhibitions-charts admin-grid admin-grid--two">
			@include('admin.components.charts.exhibition-chart')
			@include('admin.components.charts.capacity-chart')
		</div>

		<div class="exhibitions-cards admin-grid admin-grid--three">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Featured Exhibition</h3>
					<span class="admin-panel__meta">Placeholder card</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-placeholder-block admin-placeholder-block--skeleton">
						<div class="skeleton skeleton--title"></div>
						<div class="skeleton skeleton--text"></div>
					</div>
				</div>
			</div>
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Upcoming Exhibition</h3>
					<span class="admin-panel__meta">Placeholder card</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-placeholder-block admin-placeholder-block--skeleton">
						<div class="skeleton skeleton--title"></div>
						<div class="skeleton skeleton--text"></div>
					</div>
				</div>
			</div>
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Archive</h3>
					<span class="admin-panel__meta">Placeholder card</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-placeholder-block admin-placeholder-block--skeleton">
						<div class="skeleton skeleton--title"></div>
						<div class="skeleton skeleton--text"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="admin-empty-state admin-empty-state--highlight">
			<div class="admin-empty-state__icon">
				<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
			</div>
			<div class="admin-empty-state__content">
				<h3 class="admin-empty-state__title">No exhibitions scheduled</h3>
				<p class="admin-empty-state__text">Schedule updates will appear here.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Add Exhibition</button>
		</div>
	</div>
</section>
@endsection
