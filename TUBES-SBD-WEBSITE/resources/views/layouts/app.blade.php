<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'The Metropolitan Museum of Art' }}</title>

@vite(['resources/css/app.css', 'resources/css/layouts/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
</body>
</html>
