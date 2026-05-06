<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership | The Metropolitan Museum of Art</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased text-[#2D2D2D]">

    <!-- Hero Section -->
    <div class="bg-[#E0FFF9]">
        <div class="max-w-screen-2xl mx-auto flex flex-col lg:flex-row items-stretch">
            <div class="flex-1 p-8 lg:p-16 flex flex-col justify-center">
                <div class="flex items-center space-x-1 mb-12 text-[13px]">
                    <span class="underline cursor-pointer">Home</span>
                    <span class="mx-2 font-thin">/</span>
                    <span>Membership</span>
                </div>
                <h1 class="page-title mb-10">Experience more as a Met Member</h1>
                <h2 class="page-subtitle mb-6">Join or renew today!</h2>
                <p class="header-desc mb-6">
                    Discover something new on every visit and stay connected year-round. Enjoy unlimited free admission,
                    priority access to exhibitions, and invitations to Members-only events.
                </p>
                <p class="header-desc">
                    Join today and become part of our global community of supporters dedicated to helping The Met
                    educate and inspire millions each year through the power of art.
                </p>
            </div>
            <div class="flex-1">
                <img src="{{ asset('images/member-image.avif') }}" alt="Met Members" class="w-full h-full object-cover">
            </div>
        </div>
    </div>

    <!-- Membership Cards Section -->
    <div class="max-w-screen-xl mx-auto px-6 py-16">
        <h2 class="text-3xl md:text-5xl font-semibold mb-4">Membership</h2>
        <h2 class="text-xl font-extrabold mb-10">Membership pays for itself in two visits!</h2>

        <!-- Grid 3 Kolom Tetap -->
        <div class="card-grid-met">
            <!-- Individual -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Individual</h3>
                    <p class="card-price">$120 per year</p>
                    <div class="card-icons">👤 <span class="mx-1">+</span> <span class="opacity-30">👤</span></div>
                    <p class="text-sm font-semibold mb-6">1 Member Card + 1 guest</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <ul class="benefit-list bold-list">
                            <li><span>Free Admission for one Member cardholder and one guest (two total), plus children
                                    17 and under</span></li>
                            <li><span>Member Preview Days</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Express entry with Member Entrance</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Weekend Member Mornings</span> <span class="info-icon">ⓘ</span></li>
                        </ul>
                        <ul class="benefit-list mt-4">
                            <li><span>Member-only ticketed events</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Priority access in exhibition virtual queues</span></li>
                            <li><span>15% off at The Met Store (30% off seasonally)*, plus 10% discount on parking and
                                    dining</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Monthly E-newsletter, <i>What's On </i>Member Calendar, and digital
                                    <i>Bulletin</i></span></li>
                        </ul>
                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $74 is tax-deductible</p>
                </div>
            </div>

            <!-- Dual (Contoh Card 2) -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Dual</h3>
                    <p class="card-price">$220 per year</p>
                    <div class="card-icons">👤👤 <span class="mx-1">+</span> <span class="opacity-30">👤👤</span></div>
                    <p class="text-sm font-semibold mb-6">2 Member Cards + 2 guests</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <p class="italic mb-4 text-[15px]">All Individual benefits, plus:</p>
                        <ul class="benefit-list bold-list">
                            <li><span>Free Admission for two Member cardholders and two guests (four total), plus
                                    children 17 and under</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>The Met After Hours</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Access to the Balcony Lounge</span> <span class="info-icon">ⓘ</span></li>
                        </ul>

                        <ul class="benefit-list mt-4">
                            <li><span>Print subscription to the <i>Bulletin</i></span> <span class="info-icon">ⓘ</span>
                            </li>
                        </ul>
                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $114 is tax-deductible</p>
                </div>
            </div>
            <!-- Family card -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Family</h3>
                    <p class="card-price">$230 per year</p>
                    <div class="card-icons">👤👤 <span class="mx-1">+</span> <span class="opacity-30">👤👤</span></div>
                    <p class="text-sm font-semibold mb-6">2 Member Cards + 2 guests</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <p class="italic mb-4 text-[15px]">All Dual benefits, plus:</p>
                        <ul class="benefit-list bold-list">
                            <li><span>81st Street Studio Member Mornings</span> <span class="info-icon">ⓘ</span></li>
                            <li><span>Children's Classes and Camps early registration and discounts</span></li>
                        </ul>

                        <ul class="benefit-list mt-4">
                            <li><span>Two Kid's Passports, stamped on each visit</i></span>
                            <li><span>Family Member Activity Guide</i></span>
                        </ul>
                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $119 is tax-deductible</p>
                </div>
            </div>

            <!--Enthusiast card -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Enthusiast</h3>
                    <p class="card-price">$600 per year</p>
                    <div class="card-icons">👤👤 <span class="mx-1">+</span> <span class="opacity-30">👤👤👤👤</span>
                    </div>
                    <p class="text-sm font-semibold mb-6">2 Member Cards + 4 guests</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <p class="italic mb-4 text-[15px]">All Dual benefits, plus:</p>
                        <ul class="benefit-list bold-list">
                            <li><span>Free Admission for two Member cardholders, and for guests (six total), plus
                                    children 17 and under</span><span class="info-icon">ⓘ</span></li>
                            <li><span>Member Evening Receptions</span><span class="info-icon">ⓘ</span></li>
                        </ul>

                        <ul class="benefit-list mt-4">
                            <li><span>Evening with the Director</span><span class="info-icon">ⓘ</span></li>
                            <li><span>Reciprocal Benefits at 16 museums nationwide</span><span
                                    class="info-icon">ⓘ</span></li>
                            <li><span>81st Street Studio Member Mornings</span><span class="info-icon">ⓘ</span></li>
                            <li><span>Children's Classes and Camps early registration and discounts</span></li>
                        </ul>
                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $344 is tax-deductible</p>
                </div>
            </div>

            <!--Ambassador card -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Ambassador</h3>
                    <p class="card-price">$1,500 per year</p>
                    <div class="card-icons">👤👤 <span class="mx-1">+</span> <span class="opacity-30">👤👤👤👤</span>
                    </div>
                    <p class="text-sm font-semibold mb-6">2 Member Cards + 4 guests</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <p class="italic mb-4 text-[15px]">All Enthusiast benefits, plus:</p>
                        <ul class="benefit-list bold-list">
                            <li><span>Annual Curatorial Preview and Reception</span><span class="info-icon">ⓘ</span>
                            </li>
                        </ul>

                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $1,199 is tax-deductible</p>
                </div>
            </div>

            <!--Global card -->
            <div class="card-met">
                <div class="card-top">
                    <h3 class="card-type">Global</h3>
                    <p class="card-price">$90 per year</p>
                    <div class="card-icons">👤 <span class="mx-1">+</span> <span class="opacity-30">👤</span></div>
                    <p class="text-sm font-semibold mb-6">1 Member Card + 1 guest</p>
                    <button class="btn-join">Join/Renew</button>
                </div>
                <div class="card-bottom">
                    <div class="benefit-content">
                        <ul class="benefit-list bold-list">
                            <li><span>For Member outside of a 200 mile radius of the Museum</span></li>
                        </ul>
                        <p class="italic mb-4 mt-4 text-[15px]">All the benefits of the Individual level.</p>

                    </div>
                    <p class="legal-text">Within the limits prescribed by law, $44 is tax-deductible</p>
                </div>
            </div>
        </div>

        <!-- Patron Section -->
        <section class="patron-wrapper">
            <div class="patron-card">
                <div class="patron-img-box">
                    <img src="{{ asset('images/patron-image.jpg') }}" alt="Patron">
                </div>
                <div class="patron-info-box">
                    <h2 class="patron-h">Patron Program</h2>
                    <p class="patron-p">Become a Met insider with exclusive events and behind-the-scenes access and
                        deepen your impact at The Met.</p>
                    <a href="#" class="patron-link">Learn More</a>
                </div>
            </div>
        </section>

        <!-- Questions & FAQ Section -->
        <section class="mt-20 border-t border-gray-200 pt-14">
            <h2 class="text-2xl font-extrabold mb-12">Questions</h2>

            <div class="space-y-7 mb-14 max-w-4xl">
                <div>
                    <h3 class="faq-q">When do Memberships begin and can I delay the start?</h3>
                    <p class="faq-a">Your Membership is active from the date of purchase. Memberships cannot be delayed
                        and are active from the date of purchase. We recommend purchasing your personal or gift
                        Membership closer to the date you would like it to begin.</p>
                </div>
                <div>
                    <h3 class="faq-q">Can I purchase a Membership as a gift?</h3>
                    <p class="faq-a">A Membership at The Met makes the perfect gift for all occasions! Select the level
                        you'd like from the options at the top of the page, and you'll be able to enter the recipient's
                        information on the next page.</p>
                </div>
                <div>
                    <h3 class="faq-q">How long does it take to receive Membership Cards and can I visit without one?
                    </h3>
                    <p class="faq-a">It usually takes 3 to 4 weeks to receive Membership cards in the mail. Please allow
                        more time for international addresses. Members may always visit and shop without their card. If
                        you do not have your card for an upcoming visit, please check in at our Membership Desk or any
                        admissions desk with your ID number or name.</p>
                </div>
                <div>
                    <h3 class="faq-q">How do I log into my Member Portal?</h3>
                    <p class="faq-a">
                        You may access your Member Portal
                        <a href="#" class="underline font-medium hover:text-black">here</a>.
                        You may log in using the email address associated with your Membership.
                    </p>

                    <p class="faq-a mt-4">
                        If you are unsure about the password associated with your email or are unsure if you have
                        created a login,
                        please check for your account and follow the instructions
                        <a href="#" class="underline font-medium hover:text-black">here</a>.
                    </p>
                </div>
                <!-- ... tambahkan pertanyaan lainnya ke bawah ... -->
            </div>

            <!-- Container FAQ & Contact -->
            <div class="faq-contact-container">
                <!-- Tombol FAQ -->
                <div class="faq-btn-wrapper">
                    <button class="btn-faq-red">FAQs</button>
                </div>

                <!-- Teks Contact & Terms -->
                <div class="faq-text-wrapper">
                    <div class="contact-box mb-10">
                        <h4 class="contact-title">Contact Us</h4>
                        <p class="contact-detail">
                            <a href="mailto:membership@metmuseum.org"
                                class="underline-link">membership@metmuseum.org</a>
                            or call 212-570-3753
                        </p>
                    </div>

                    <div class="terms-box">
                        <h4 class="contact-title">Terms and Conditions</h4>
                        <p class="terms-text">
                            Please note that contributions are not refundable or transferable. All categories, benefits,
                            and prices are subject to change. Membership benefits may not be refunded, resold,
                            exchanged, or transferred. Admission tickets and membership benefits may not be used for
                            group visits. By visiting the Museum, you agree to comply with our <a href="#"
                                class="underline-link">Visitor Guidelines</a>.
                        </p>
                        <p class="terms-text mt-4">*Retail Discount Terms and Conditions</p>
                        <p class="terms-text mt-4">Discount does not apply to the purchase of museum admission, gift
                            memberships, donations, eGift Certificates, prior purchases, clearance products or select
                            items. Cannot be combined with other offers or discounts. Offer subject to change or
                            cancellation without notice.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="met-footer">
            <div class="footer-container">
                <!-- Footer Links -->
                <nav class="footer-nav">
                    <a href="#" class="footer-link">Site Index</a>
                    <a href="#" class="footer-link">Terms and Conditions</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Contact Information</a>
                </nav>

                <!-- Copyright -->
                <p class="footer-copyright">
                    © 2000–2025 The Metropolitan Museum of Art. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>

</html>