@extends('layouts.main')

@section('title', 'The Met Collection')

@section('content')

<section class="w-full bg-white font-sans">

  <!-- BREADCRUMB -->
  <div class="w-full border-b border-[#e5e5e5] bg-white">
    <div class="max-w-[1600px] mx-auto px-16 h-[84px] flex items-center gap-4">

      <!-- HOME ICON -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-[13px] h-[13px] text-black hover:fill-black"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3 10.75L12 3l9 7.75V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.25z"
        />
      </svg>

      <span class="text-black text-[13px] leading-none">/</span>

      <a
        href="{{ route('art.index') }}"
        class="text-[14px] font-semibold text-black hover:underline"
      >
        The Met Collection
      </a>

    </div>
  </div>

  <!-- HERO IMAGE -->
  <div class="w-full overflow-hidden">
    <img
      src="{{ asset('images/met-collection.avif')}}"
      alt="The Met Collection"
      class="w-full h-[250px] md:h-[250px] lg:h-[550px] object-cover"
    />
  </div>

  <!-- CONTENT -->
  <div class="max-w-[1600px] mx-auto px-16 pt-10 pb-20">

    <h1 class="text-[88px] md:text-[36px] font-bold text-black tracking-tight leading-none">
      The Met Collection
    </h1>
    <p class="mt-2 text-[20px] lg:text-[26px] text-black max-w-[1200px] font-light">
      Travel around the world and across 5,000 years of history through 490,000+ works of art.
    </p>

  </div>

  <section class="w-full bg-white font-sans">

  <div class="max-w-[1550px] mx-auto px-16">

    <!-- SEARCH -->
    <form action="{{ route('art.search') }}" method="GET" class="flex items-center w-full border border-[#cfcfcf]  bg-white h-[58px] rounded-md">

      <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="Search The Collection"
        class="flex-1 h-full px-6 text-[16px] text-gray-600 placeholder:text-[#9b9b9b] font-extralight outline-none bg-transparent"
      />

      <button
        type="submit"
        class="w-[90px] h-full border-l border-[#cfcfcf] flex items-center justify-center hover:bg-[#f0f0f0] transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-6 h-6 text-[#666]"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
          />
        </svg>
      </button>

    </form>

    <!-- QUICK LINKS -->
    <div class="flex items-center gap-3 mt-7 flex-wrap">

      <a
        href="{{ route('art.search') }}"
        class="flex items-center gap-2 text-[16px] text-[#111] font-medium hover:underline"
      >
        Advanced Collection Search
      </a>
      <span class="text-[25px] leading-none text-[#444]">›</span>

      <a
        href="{{ route('art.index') }}"
        class="flex items-center gap-2 text-[16px] text-[#111] font-medium hover:underline"
      >
        Highlight
      </a>
       <span class="text-[25px] leading-none text-[#444]">›</span>

      <a
        href="{{ route('art.index') }}"
        class="flex items-center gap-2 text-[16px] text-[#111] font-medium hover:underline"
      >
        Public Domain Images
      </a>
      <span class="text-[25px] leading-none text-[#444]">›</span>

    </div>

    <!-- LINE -->
    <div class="w-full h-[1px] bg-[#dddddd] mt-8"></div>

    <!-- TITLE -->
    <h3 class="text-[27px] font-semibold text-black tracking-[-1px] mt-12 mb-2">
      Browse the Art
    </h3>

    <!-- GRID -->
    <x-department-navigation-grid :items="$departmentNavigationItems ?? []" />

  </div>

</section>


@endsection
