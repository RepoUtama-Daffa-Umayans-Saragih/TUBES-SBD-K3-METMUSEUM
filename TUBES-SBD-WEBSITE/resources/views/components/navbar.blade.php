@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/components/navbar.css')
@endpush

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="The Met Museum" class="logo-img">
                <span class="logo-text">The Metropolitan Museum</span>
            </a>
        </div>

        <ul class="navbar-menu">
            <li><a href="{{ route('art.index') }}" class="nav-link">Collections</a></li>
            <li><a href="{{ route('plan-your-visit.index') }}" class="nav-link">Visit</a></li>
            <li><a href="{{ route('ticket.admission') }}" class="nav-link">Tickets</a></li>
            <li><a href="{{ route('membership.index') }}" class="nav-link">Membership</a></li>
            <li><a href="{{ route('about') }}" class="nav-link">About</a></li>
        </ul>

        <div class="navbar-auth">
            @if(auth()->check() || session('guest_id'))
                <div class="user-menu">

                    {{-- USER --}}
                    @if(auth()->check())
                        <span class="user-name">{{ auth()->user()->email }}</span>

                        {{-- ✅ ORDER (HANYA USER) --}}
                        <a href="{{ route('order.show') }}" class="nav-link">Order</a>
                    @else
                        {{-- GUEST --}}
                        <span class="user-name">Guest</span>
                    @endif

                    {{-- CART (USER + GUEST) --}}
                    <a href="{{ route('ticket.cart') }}" class="nav-link">Cart</a>

                    {{-- LOGOUT --}}
                    <form action="{{ route('account.logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">Logout</button>
                    </form>

                </div>
            @else
                {{-- BELUM LOGIN --}}
                <a href="{{ route('account.login') }}" class="nav-link btn-login">Login</a>
                <a href="{{ route('account.register') }}" class="nav-link btn-register">Register</a>
            @endif
        </div>
    </div>
</nav>