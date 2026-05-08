<nav class="bg-[#961d31] text-white sticky top-0 z-50">
    <style>
        .menu-trigger.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -0.35rem;
            width: 100%;
            height: 2px;
            background: #fff;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-4">
                <div class="text-[1.9rem] font-black leading-none tracking-[-0.08em]">THE<br>MET</div>
            </a>

            <div class="hidden xl:flex items-center gap-10 text-sm font-semibold tracking-[0.02em]">
                <button type="button" data-menu="visit" class="menu-trigger relative pb-2 text-white/90 hover:text-white transition">Visit</button>
                <button type="button" data-menu="exhibitions" class="menu-trigger relative pb-2 text-white/90 hover:text-white transition">Exhibitions and Events</button>
                <button type="button" data-menu="art" class="menu-trigger relative pb-2 text-white/90 hover:text-white transition">Art</button>
                <button type="button" data-menu="learn" class="menu-trigger relative pb-2 text-white/90 hover:text-white transition">Learn with Us</button>
                <button type="button" data-menu="research" class="menu-trigger relative pb-2 text-white/90 hover:text-white transition">Research</button>
                <a href="#" class="relative pb-2 text-white/90 hover:text-white transition inline-flex items-center gap-1">Shop <span class="text-xs tracking-[0.2em]">↗</span></a>
            </div>

            <div class="flex items-center gap-3 text-sm font-medium">
                <button class="text-white/90 hover:text-white transition" aria-label="Search">
                    <i class="fas fa-search text-lg"></i>
                </button>
                <a href="#" class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">
                    <i class="fas fa-globe"></i>
                    English
                </a>

                <a href="{{ route('ticket.cart') }}" class="rounded-full border border-white/30 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>

                @auth
                    <a href="{{ route('order.show') }}" class="rounded-full border border-white/30 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">Orders</a>
                    <form action="{{ route('account.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-full border border-white/30 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('account.login') }}" class="rounded-full border border-white/30 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">Login</a>
                @endguest

                <a href="{{ route('membership.index') }}" class="rounded-full border border-white/30 px-4 py-2 text-white/90 hover:text-white hover:border-white transition">Membership</a>
                <a href="{{ route('ticket.index') }}" class="rounded-full bg-white px-4 py-2 text-[#961d31] font-semibold shadow-sm hover:bg-gray-100 transition">Tickets</a>
            </div>
        </div>
    </div>

    <div id="mega-menu" class="hidden border-t border-white/20 bg-[#961d31]">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid gap-10 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,420px)]">
                <div class="grid gap-5" data-menu-content="visit">
                    <a href="{{ route('plan-your-visit.index') }}" class="text-base font-semibold text-white hover:text-white/80 transition">Plan Your Visit</a>
                    <a href="{{ route('ticket.index') }}" class="text-base font-semibold text-white hover:text-white/80 transition">Buy Tickets</a>
                    <a href="{{ route('membership.index') }}" class="text-base font-semibold text-white hover:text-white/80 transition">Become a Member</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Free Tours</a>
                </div>
                <div class="grid gap-5" data-menu-content="visit">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Museum Map</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Food and Drink</a>
                    <a href="{{ route('visit.accessibility') }}" class="text-base font-semibold text-white hover:text-white/80 transition">Accessibility</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Group Visits</a>
                </div>
                <div class="flex items-center" data-menu-content="visit">
                    <div class="border-l border-white/30 pl-8">
                        <p class="text-3xl font-semibold leading-tight text-white">Make the most of your visit</p>
                    </div>
                </div>

                <div class="grid gap-5 hidden" data-menu-content="exhibitions">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Exhibitions</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Events</a>
                </div>
                <div class="grid gap-5 hidden" data-menu-content="exhibitions">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Free Tours</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Performances</a>
                </div>
                <div class="flex items-center hidden" data-menu-content="exhibitions">
                    <div class="border-l border-white/30 pl-8">
                        <p class="text-3xl font-semibold leading-tight text-white">What to see and do</p>
                    </div>
                </div>

                <div class="grid gap-5 hidden" data-menu-content="art">
                    <a href="{{ route('art.index') }}" class="text-base font-semibold text-white hover:text-white/80 transition">The Met Collection</a>
                    <a href="{{ route('art.curatorial-areas') }}" class="text-base font-semibold text-white hover:text-white/80 transition">Curatorial Areas</a>
                </div>
                <div class="grid gap-5 hidden" data-menu-content="art">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Conservation and Scientific Research</a>
                    
                </div>
                <div class="flex items-center hidden" data-menu-content="art">
                    <div class="border-l border-white/30 pl-8">
                        <p class="text-3xl font-semibold leading-tight text-white">Explore art across time and place</p>
                    </div>
                </div>

                <div class="grid gap-5 hidden" data-menu-content="learn">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Learning Resources</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Publications</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Timeline of Art History</a>
                </div>
                <div class="grid gap-5 hidden" data-menu-content="learn">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Workshops and Activities</a>
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Articles, Videos, and Podcasts</a>
                </div>
                <div class="flex items-center hidden" data-menu-content="learn">
                    <div class="border-l border-white/30 pl-8">
                        <p class="text-3xl font-semibold leading-tight text-white">Spend a few minutes or take a deep dive</p>
                    </div>
                </div>

                <div class="grid gap-5 hidden" data-menu-content="research">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Research at The Met</a>
                </div>
                <div class="grid gap-5 hidden" data-menu-content="research">
                    <a href="#" class="text-base font-semibold text-white hover:text-white/80 transition">Provenance Research</a>
                </div>
                <div class="flex items-center hidden" data-menu-content="research">
                    <div class="border-l border-white/30 pl-8">
                        <p class="text-3xl font-semibold leading-tight text-white">Discover unparalleled resources</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuButtons = document.querySelectorAll('.menu-trigger');
            const megaMenu = document.getElementById('mega-menu');
            const contents = document.querySelectorAll('[data-menu-content]');

            function closeMenu() {
                contents.forEach(item => item.classList.add('hidden'));
                megaMenu.classList.add('hidden');
                menuButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('text-white');
                });
            }

            function openMenu(name, button) {
                closeMenu();
                    const showItems = document.querySelectorAll(`[data-menu-content="${name}"]`);
                showItems.forEach(item => item.classList.remove('hidden'));
                megaMenu.classList.remove('hidden');
                button.classList.add('active');
                button.classList.add('text-white');
            }

            menuButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const name = button.getAttribute('data-menu');
                    const active = !megaMenu.classList.contains('hidden') && button.classList.contains('active');
                    if (active) {
                        closeMenu();
                        return;
                    }
                    openMenu(name, button);
                });
            });

            document.addEventListener('click', (event) => {
                const isTrigger = event.target.closest('.menu-trigger');
                const isMenu = event.target.closest('#mega-menu');
                if (!isTrigger && !isMenu) {
                    closeMenu();
                }
            });
        });
    </script>
</nav>
