<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Account' }} - The Met</title>

    @stack('styles')
</head>
<body>
    <header class="met-topbar">
        <div class="met-topbar-inner">
            <a href="{{ route('home') }}" class="met-logo" aria-label="The Metropolitan Museum of Art home">
                <span>THE</span>
                <span>MET</span>
            </a>

            <nav class="met-nav" aria-label="Account navigation">
                <a href="{{ route('account.login') }}" class="met-nav-link">
                    <span class="met-nav-icon met-nav-icon-user" aria-hidden="true"></span>
                    <span>Login</span>
                </a>
                <a href="{{ route('ticket.cart') }}" class="met-nav-link">
                    <span class="met-nav-icon met-nav-icon-cart" aria-hidden="true"></span>
                    <span>Cart</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="met-page">
        @yield('content')
    </main>

    <footer class="met-footer">
        <div class="met-footer-inner">
            <nav class="met-footer-links" aria-label="Footer navigation">
                <a href="#">Site Index</a>
                <a href="#">Terms and Conditions</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Contact Information</a>
            </nav>
            <p>&copy; 2000-2025 The Metropolitan Museum of Art. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
