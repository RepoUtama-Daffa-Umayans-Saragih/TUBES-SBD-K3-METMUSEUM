@extends('layouts.app')

@push('styles')
@vite('resources/css/app.css')
@vite('resources/css/ordinary/membership/show/show.css')
@endpush

@section('title', 'Membership Details - MET Museum')
@section('page_title', 'Membership Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('membership.index') }}" class="text-blue-600 hover:text-blue-800 mb-6 inline-flex items-center">
            <span class="mr-2">←</span> Back to Memberships
        </a>

        @if($membership)
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Membership Header -->
                <div class="border-b pb-6 mb-6">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $membership['name'] }}</h1>
                    <p class="text-gray-600">Annual Membership Plan</p>
                </div>

                <!-- Price Section -->
                <div class="mb-8">
                    <div class="flex items-baseline">
                        <span class="text-5xl font-bold text-gray-900">${{ number_format($membership['price'], 2) }}</span>
                        <span class="text-gray-600 ml-2">{{ $membership['duration'] }}</span>
                    </div>
                </div>

                <!-- Features List -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Membership Benefits</h2>
                    <ul class="space-y-3">
                        @forelse($membership['features'] ?? [] as $feature)
                            <li class="flex items-start">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @empty
                            <li class="text-gray-600">No features listed for this membership.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Purchase Button -->
                <div class="border-t pt-6">
                    @auth
                        <form action="{{ route('membership.purchase') }}" method="POST">
                            @csrf
                            <input type="hidden" name="membership_id" value="{{ $membership['id'] }}">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                                Purchase Now
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                            Login to Purchase
                        </a>
                    @endauth
                </div>

                <!-- Additional Information -->
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h3 class="font-bold text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-gray-600 text-sm">
                        Contact our membership team at <a href="mailto:membership@metmuseum.org" class="text-blue-600 hover:underline">membership@metmuseum.org</a>
                        or call (212) 535-7710 for more information about this membership plan.
                    </p>
                </div>
            </div>
        @else
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <p class="text-red-800">Membership details not found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
