
@extends('admin.layout.layout')

@push('styles')
@vite('resources/css/admin/artworks/index.css')
@endpush

@section('admin-title')
    Artworks
@endsection

@section('admin-content')
<section class="admin-section admin-section--artworks admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'Artwork Management',
		'subtitle' => 'Maintain the museum collection catalog.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Artworks', 'isCurrent' => true],
		],
		'status' => ['label' => 'Archived', 'tone' => 'archived'],
		'badges' => [
			['label' => 'Collection', 'tone' => 'neutral'],
			['label' => 'Catalog', 'tone' => 'info'],
		],
		'filters' => ['On Display', 'Storage', 'On Loan'],
		'actions' => [
			['label' => 'Add Artwork', 'variant' => 'secondary'],
			['label' => 'Export Catalog', 'variant' => 'primary'],
		],
	])

	<div class="artworks-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		@include('admin.components.filters.filter-bar')

		<div class="artworks-layout admin-grid admin-grid--main">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Artwork Collection</h3>
					<span class="admin-panel__meta">Grid placeholder</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-placeholder-block admin-placeholder-block--tall admin-placeholder-block--skeleton">
						<div class="skeleton skeleton--title"></div>
						<div class="skeleton skeleton--text"></div>
						<div class="skeleton skeleton--text"></div>
					</div>
				</div>
			</div>
			<div class="artworks-side admin-stack">
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Artwork Metadata</h3>
						<span class="admin-panel__meta">Placeholder fields</span>
					</div>
					<div class="admin-panel__body admin-list">
						<div class="admin-list__item">Title</div>
						<div class="admin-list__item">Artist</div>
						<div class="admin-list__item">Period</div>
						<div class="admin-list__item">Collection</div>
					</div>
				</div>
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Tags and Classification</h3>
						<span class="admin-panel__meta">Placeholder tags</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-pill-group">
							<span class="admin-pill">Modern</span>
							<span class="admin-pill">Sculpture</span>
							<span class="admin-pill">On Loan</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="artworks-bottom admin-grid admin-grid--two">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Artwork Preview</h3>
					<span class="admin-panel__meta">Image placeholder</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-image-placeholder"></div>
				</div>
			</div>
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Artwork Analytics</h3>
					<span class="admin-panel__meta">Placeholder insights</span>
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
				<h3 class="admin-empty-state__title">No artworks yet</h3>
				<p class="admin-empty-state__text">Artwork updates will appear here.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Add Artwork</button>
		</div>
	</div>
</section>
@endsection
