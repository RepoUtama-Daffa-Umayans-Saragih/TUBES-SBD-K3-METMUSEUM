@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/membership/membership.css')
@endpush

@section('content')
<div class="container">
    <div class="membership-container">
        <div class="membership-header">
            <h1>Museum Membership</h1>
            <p>Become a member and enjoy unlimited access to The Met</p>
        </div>

        <div class="membership-grid">
            <div class="membership-card">
                <div class="membership-tier">
                    <h3>Individual</h3>
                    <p class="membership-price">$99/year</p>
                </div>
                <ul class="membership-features">
                    <li>Unlimited admission</li>
                    <li>Member events</li>
                    <li>10% gift shop discount</li>
                    <li>Member magazine</li>
                </ul>
                <button class="btn btn-primary btn-block">Join Now</button>
            </div>

            <div class="membership-card featured">
                <div class="featured-badge">Most Popular</div>
                <div class="membership-tier">
                    <h3>Family</h3>
                    <p class="membership-price">$199/year</p>
                </div>
                <ul class="membership-features">
                    <li>Unlimited admission for 2 adults + 1 child</li>
                    <li>Member events</li>
                    <li>15% gift shop discount</li>
                    <li>Member magazine</li>
                    <li>Priority access to exhibitions</li>
                </ul>
                <button class="btn btn-primary btn-block">Join Now</button>
            </div>

            <div class="membership-card">
                <div class="membership-tier">
                    <h3>Patron</h3>
                    <p class="membership-price">$500/year</p>
                </div>
                <ul class="membership-features">
                    <li>Unlimited admission + up to 4 guests</li>
                    <li>VIP events access</li>
                    <li>20% gift shop discount</li>
                    <li>Member magazine</li>
                    <li>Priority access to exhibitions</li>
                    <li>Exclusive Patron benefits</li>
                </ul>
                <button class="btn btn-primary btn-block">Join Now</button>
            </div>
        </div>
    </div>
</div>
@endsection
