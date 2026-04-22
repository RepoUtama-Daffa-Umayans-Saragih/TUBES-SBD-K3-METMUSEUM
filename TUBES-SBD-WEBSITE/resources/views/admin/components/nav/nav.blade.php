@vite('resources/css/app.css')
@vite('resources/css/admin/components/nav/nav.css')
<!-- Admin Navigation Sidebar -->

<div class="admin-sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>
        📊 Dashboard
    </a>
    <a href="{{ route('admin.art.index') }}" @class(['active' => request()->routeIs('admin.art.*')])>
        🎨 Artworks
    </a>
    <a href="{{ route('home') }}">← Back to Site</a>
    <div class="admin-user-info user-info-spacer">
        {{ Auth::user()->name }} ({{ Auth::user()->role }})
    </div>
</div>
