<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission | The Metropolitan Museum of Art</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white antialiased text-[#1a1a1a]">

    <div class="admission-wrapper">

        <!-- STEP NAVIGATION -->
        <nav class="step-nav">
            <div class="step-item active">
                <span>Select</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
            <div class="step-item inactive">
                <span>Add</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
            <div class="step-item inactive">
                <span>Review</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
            <div class="step-item inactive">
                <span>Pay</span>
            </div>
        </nav>

        <h1 class="admission-title">
            Admission<br>Tickets
        </h1>
        <!-- MAIN CONTENT GRID -->
        <main class="admission-grid">

            <!-- LEFT COLUMN: Content -->
            <section class="admission-left">

                <div class="policy-section">
                    <h2 class="section-subtitle">
                        New York State residents and NY, NJ, and CT students:
                    </h2>
                    <ul class="info-list">
                        <li>The amount you pay for admission is up to you, but you must pay something.</li>
                        <li>To buy pay-what-you-wish tickets online, you must have a New York State billing address.
                        </li>
                        <li>New Jersey and Connecticut students can only buy pay-what-you-wish tickets in person with
                            valid student ID.</li>
                    </ul>
                </div>

                <div class="info-footer">
                    <p class="desc-text">
                        The <a href="#" class="link-styled">galleries for the Art of Ancient West Asia and the
                            Art of Ancient Cyprus</a> are closed for renovation. The Temple of Dendur will be closed
                        Sunday, April 26 through Friday, May 8. The Charles Engelhard Court in the American Wing will be
                        closed for Saturday, May 2 and Sunday, May 3. The Met Fifth Avenue will be closed on Monday, May
                        4.
                    </p>
                    <a href="#" class="link-styled text-xs mt-1 block mb-2">See a list of currently closed
                        galleries.</a>

                    <p class="desc-text text-gray-700 mb-2">Members: Your card is your ticket. Once inside,
                        proceed to a gallery entrance and present your card.</p>
                    <p class="desc-text text-gray-700">Visiting in a group? For information about bringing an
                        adult or student group or scheduling a group tour, see <a href="#" class="link-styled">Group
                            Visits</a>.</p>
                </div>

                <div class="member-card">
                    <div class="member-content">
                        <h2 class="member-title">Become a Member</h2>
                        <p class="member-text">
                            Enjoy <span class="font-bold">unlimited free admission</span> for you and your guest(s) on
                            every visit.
                        </p>
                    </div>
                    <div class="member-action">
                        <a href="#" class="btn-join">Join today</h6></a>
                    </div>
                </div>
            </section>

            <!-- RIGHT COLUMN: Sidebar (Price Card) -->
            <aside class="admission-right">
                <div class="price-card">
                    <h3 class="card-title">General Admission Tickets</h3>

                    <div class="price-item">
                        <span class="price-main">$30 for adults; $22 for seniors; $17 for students.</span>
                        <hr class="card-divider">
                    </div>

                    <div class="price-item">
                        <span class="price-main">Free for Members, Patrons, and children 12 and under.</span>
                        <hr class="card-divider">
                    </div>

                    <div class="price-item">
                        <span class="price-main">$22 for visitors with a disability; free for a care partner of a
                            visitor with a disability (in person only).
                        </span>
                        <hr class="card-divider">
                    </div>

                    <div class="price-item">
                        <span class="price-main">All tickets include exhibitions and same-day entry to both Met
                            locations for the date on your ticket.
                        </span>
                        <hr class="card-divider">
                    </div>

                    <div class="price-item">
                        <a href="#" class="link-styled text-xs mt-1 block mb-2">Learn more about other discounts,
                            passes, and vouchers.
                        </a>
                    </div>
            </aside>
        </main>

        <!-- Tambahkan padding bottom di container luar agar konten tidak tertutup bar mobile -->
        <div class="max-w-6xl mx-auto px-4 pb-32 md:pb-12">

            <section class="location-grid">
                <!-- Kolom Kiri -->
                <div class="md:col-span-2">
                    <h2 class="location-title">1. Choose a location</h2>
                    <p class="text-gray-700 text-sm font-normal tracking-tight mb-6">Choose one of two Met locations.
                    </p>

                    <div class="space-y-3">
                        <!-- Card 1 -->
                        <div class="card-location">
                            <div class="card-img-container">
                                <img src="{{asset('images/choose-location.jpg')}}" alt="The Met Fifth Avenue"
                                    class="card-img">
                            </div>
                            <div class="card-content">
                                <h3 class="font-extrabold text-lg">The Met Fifth Avenue</h3>
                                <p class="text-xs text-gray-600 mb-1">Sunday-Tuesday and Thursday: 10 am-5 pm</p>
                                <p class="text-xs text-gray-600 mb-1">Sunday and Saturday: 10 am-9 pm</p>
                                <p class="text-xs text-gray-600">Closed Wednesday</p>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="card-location">
                            <div class="card-img-container">
                                <img src="{{asset('images/choose-location2.jpg')}}" alt="The Met Cloisters"
                                    class="card-img">
                            </div>
                            <div class="card-content">
                                <h3 class="font-extrabold text-lg">The Met Cloisters</h3>
                                <p class="text-xs text-gray-600 mb-1">Thursday–Tuesday: 10 am–5 pm</p>
                                <p class="text-xs text-gray-600">Closed Wednesday</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <h2 class="location-title">2. Select a date</h2>
                        <p class="select-location"><a href="#" class="select-link">Select a location </a>to see
                            available dates.</p>
                    </div>
                </div>

                <!-- Kolom Kanan (Subtotal) -->
                <div class="relative">
                    <div class="sticky-subtotal">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-bold md:text-lg">Subtotal</span>
                            <span class="text-gray-700 font-semibold md:text-lg">$0.00</span>
                        </div>
                        <button class="btn-next">
                            Next
                        </button>
                    </div>
                </div>
            </section>

        </div>
    </div>

    </div>

</body>

</html>