@extends('layouts.main')

@section('title', 'Home')

@section('content')
    <section class="min-h-[calc(100vh-6rem)] bg-[#b6b6b6] flex items-center justify-center">
        <div class="w-full max-w-4xl px-6 py-24 text-center">
            <h1 class="text-5xl md:text-6xl font-semibold tracking-tight text-white leading-tight">Welcome to The Met</h1>
            <p class="mx-auto mt-8 max-w-3xl text-base md:text-lg text-white/90 leading-relaxed">Experience art, exhibitions, and culture from one of the world’s greatest museums.</p>
            <a href="{{ route('plan-your-visit.index') }}" class="mt-12 inline-flex items-center justify-center rounded-full border border-white px-10 py-3 text-base font-semibold text-white transition hover:bg-white/15">Plan your visit</a>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-gray-500">Now On View</p>
                    <h2 class="mt-3 text-3xl font-semibold text-gray-900">Current exhibitions and highlights</h2>
                </div>
                <div class="flex items-center gap-3">
                    <button id="now-on-view-prev" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-gray-300 text-gray-700 transition hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="now-on-view-next" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-gray-300 text-gray-700 transition hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-10 overflow-hidden">
                <div id="now-on-view-track" class="grid gap-6 grid-flow-col auto-cols-[minmax(340px,1fr)] transition-transform duration-500">
                    @foreach(range(1, 10) as $i)
                        <article class="group relative overflow-hidden rounded-3xl bg-white shadow-sm">
                            <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                                <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=80" alt="Exhibition {{ $i }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="p-6">
                                <p class="text-xl font-semibold text-gray-900">Exhibition {{ $i }}</p>
                                <p class="mt-2 text-sm text-gray-500">Through August {{ 5 + $i }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-semibold text-gray-900">Locations and Hours</h2>

            <div class="mt-10 grid gap-8 lg:grid-cols-2">
                <article class="overflow-hidden rounded-3xl bg-white shadow-sm">
                    <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1577720643272-265a6e3adb98?auto=format&fit=crop&w=1200&q=80" alt="The Met Fifth Avenue" class="h-full w-full object-cover">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-semibold text-gray-900">The Met Fifth Avenue</h3>
                        <p class="mt-4 text-sm text-gray-600"><strong>Hours:</strong> Sunday–Tuesday and Thursday: 10 am–5 pm</p>
                        <p class="text-sm text-gray-600"><strong>Extended Hours:</strong> Friday and Saturday: 10 am–9 pm</p>
                        <p class="text-sm text-gray-600"><strong>Closed:</strong> Wednesday</p>
                        <p class="mt-4 text-xs text-gray-500">Closed Thanksgiving Day, December 25, January 1, and the first Monday in May.</p>
                    </div>
                </article>

                <article class="overflow-hidden rounded-3xl bg-white shadow-sm">
                    <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1512207736139-aea4b5a1e71f?auto=format&fit=crop&w=1200&q=80" alt="The Met Cloisters" class="h-full w-full object-cover">
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-semibold text-gray-900">The Met Cloisters</h3>
                        <p class="mt-4 text-sm text-gray-600"><strong>Hours:</strong> Thursday–Tuesday: 10 am–5 pm</p>
                        <p class="mt-4 text-xs text-gray-500">Closed Thanksgiving Day, December 25, and January 1.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid gap-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div class="overflow-hidden rounded-3xl bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1494522358652-9e05a0d0b737?auto=format&fit=crop&w=1200&q=80" alt="The Met Gala red carpet" class="h-full w-full object-cover">
                </div>
                <div class="max-w-2xl">
                    <h3 class="text-3xl font-semibold text-gray-900">The Met Gala® Red Carpet Livestream</h3>
                    <p class="mt-6 text-lg text-gray-600 leading-8">See how guests arrived at the 2026 Costume Institute Benefit—also known as The Met Gala®—celebrating Costume Art.</p>
                    <a href="#" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-red-600">
                        Watch now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid gap-16 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                <div class="max-w-2xl">
                    <h3 class="text-3xl font-semibold text-gray-900">Asian American and Pacific Islander Heritage Month</h3>
                    <p class="mt-6 text-lg text-gray-600 leading-8">Celebrate Asian American and Pacific Islander Heritage Month through art, talks, and more.</p>
                    <a href="#" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-red-600">
                        Learn more
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="overflow-hidden rounded-3xl bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1544934521-9a1518bb0ef8?auto=format&fit=crop&w=1200&q=80" alt="Asian American and Pacific Islander Heritage" class="h-full w-full object-cover">
                </div>
            </div>

            <div class="mt-16 grid gap-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div class="overflow-hidden rounded-3xl bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1512207736139-aea4b5a1e71f?auto=format&fit=crop&w=1200&q=80" alt="Spring at The Met" class="h-full w-full object-cover">
                </div>
                <div class="max-w-2xl">
                    <h3 class="text-3xl font-semibold text-gray-900">Spring at The Met</h3>
                    <p class="mt-6 text-lg text-gray-600 leading-8">Welcome the season with iconic exhibitions, free tours, events, and performances.</p>
                    <a href="#" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-red-600">
                        Explore now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid gap-16 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-semibold text-gray-900">Become a Member Today</h3>
                    <p class="mt-6 text-lg text-gray-600 leading-8">Members get special access to Raphael: Sublime Poetry with Weekend Member Mornings and special Evening Viewings. Join by June 28 for a chance to win the ultimate keepsake: a curator-signed exhibition catalogue.</p>
                    <a href="#" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-red-600">
                        Join now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="overflow-hidden rounded-3xl bg-black">
                    <img src="https://images.unsplash.com/photo-1511275539161-0f33143958f3?auto=format&fit=crop&w=1200&q=80" alt="Circular painting" class="h-full w-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid gap-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div class="overflow-hidden rounded-3xl">
                    <img src="https://images.unsplash.com/photo-1516819604558-719260e0e04f?auto=format&fit=crop&w=1200&q=80" alt="Met store book" class="h-full w-full object-cover">
                </div>
                <div class="max-w-xl">
                    <h3 class="text-2xl font-semibold text-gray-900">Shop the Show</h3>
                    <p class="mt-6 text-lg text-gray-600 leading-8">Discover exclusive designs celebrating Costume Art, The Costume Institute’s spring 2026 exhibition.</p>
                    <a href="#" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 hover:text-red-600">
                        Visit The Met Store
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('now-on-view-track');
            const prev = document.getElementById('now-on-view-prev');
            const next = document.getElementById('now-on-view-next');
            const cards = track.querySelectorAll('article');
            let index = 0;
            const visibleCount = 3;
            const total = cards.length;

            function updatePosition() {
                const cardWidth = cards[0].getBoundingClientRect().width + 24;
                const distance = Math.min(index, total - visibleCount) * cardWidth;
                track.style.transform = `translateX(-${distance}px)`;
            }

            prev.addEventListener('click', () => {
                index = Math.max(index - 1, 0);
                updatePosition();
            });

            next.addEventListener('click', () => {
                index = Math.min(index + 1, total - visibleCount);
                updatePosition();
            });
        });
    </script>
@endsection

