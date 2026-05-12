
@extends('admin.layouts.admin-layout')

@push('styles')
@vite('resources/css/admin/users/index.css')
@endpush

@section('title', 'Users')

@section('content')
<section class="admin-section admin-section--users admin-page">
	@include('admin.components.toolbar.page-toolbar', [
		'title' => 'User Management',
		'subtitle' => 'Manage guests, members, and administrators.',
		'breadcrumbs' => [
			['label' => 'Dashboard', 'href' => '#'],
			['label' => 'Users', 'isCurrent' => true],
		],
		'status' => ['label' => 'Active', 'tone' => 'active'],
		'badges' => [
			['label' => 'Directory', 'tone' => 'neutral'],
			['label' => 'Guests', 'tone' => 'info'],
		],
		'filters' => ['Members', 'Guests', 'Staff'],
		'actions' => [
			['label' => 'Invite User', 'variant' => 'secondary'],
			['label' => 'Export Directory', 'variant' => 'primary'],
		],
	])

	<div class="users-summary admin-grid admin-grid--cards">
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.stat-card')
		@include('admin.components.cards.skeleton-card')
	</div>

	<div class="admin-section__body admin-stack">
		@include('admin.components.filters.filter-bar')

		<div class="users-layout admin-grid admin-grid--main">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Users Table</h3>
					<span class="admin-panel__meta">Directory list</span>
				</div>
				@include('admin.components.tables.data-table')
			</div>
			<div class="users-side admin-stack">
				<div class="admin-panel users-profile">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Profile Preview</h3>
						<span class="admin-panel__meta">Placeholder profile</span>
					</div>
					<div class="admin-panel__body">
						<div class="admin-avatar-placeholder"></div>
						<div class="admin-placeholder-block admin-placeholder-block--skeleton">
							<div class="skeleton skeleton--title"></div>
							<div class="skeleton skeleton--text"></div>
							<div class="skeleton skeleton--text"></div>
						</div>
					</div>
				</div>
				<div class="admin-panel">
					<div class="admin-panel__header">
						<h3 class="admin-panel__title">Recent Activity</h3>
						<span class="admin-panel__meta">Placeholder list</span>
					</div>
					<div class="admin-panel__body admin-list">
						<div class="admin-list__item">Login placeholder</div>
						<div class="admin-list__item">Ticket purchase placeholder</div>
					</div>
				</div>
			</div>
		</div>

		<div class="users-segments admin-grid admin-grid--two">
			<div class="admin-panel">
				<div class="admin-panel__header">
					<h3 class="admin-panel__title">Members</h3>
					<span class="admin-panel__meta">Membership overview</span>
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
					<h3 class="admin-panel__title">Guests</h3>
					<span class="admin-panel__meta">Guest overview</span>
				</div>
				<div class="admin-panel__body">
					<div class="admin-placeholder-block admin-placeholder-block--skeleton">
						<div class="skeleton skeleton--title"></div>
						<div class="skeleton skeleton--text"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="admin-empty-state admin-empty-state--compact">
			<div class="admin-empty-state__icon">
				<span class="icon-placeholder icon-placeholder--sm" aria-hidden="true"></span>
			</div>
			<div class="admin-empty-state__content">
				<h3 class="admin-empty-state__title">No user activity</h3>
				<p class="admin-empty-state__text">User events will appear here.</p>
			</div>
			<button class="admin-button admin-button--ghost" type="button">Add User</button>
		</div>
	</div>
</section>
@endsection
