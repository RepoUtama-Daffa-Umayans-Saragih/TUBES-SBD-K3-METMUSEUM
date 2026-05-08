@extends('layouts.main')

@section('title', 'The Met Collection')

@section('content')

<section class="w-full bg-[#f5f5f5] font-sans">

  <!-- BREADCRUMB -->
  <div class="w-full border-b border-[#e5e5e5] bg-[#f5f5f5]">
    <div class="max-w-[1600px] mx-auto px-16 h-[84px] flex items-center gap-4">
      
      <!-- HOME ICON -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-[16px] h-[16px] text-[#444]"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3 10.75L12 3l9 7.75V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.25z"
        />
      </svg>

      <span class="text-[#777] text-[28px] leading-none">/</span>

      <a
        href="#"
        class="text-[18px] font-semibold text-[#222] hover:underline"
      >
        The Met Collection
      </a>

    </div>
  </div>

  <!-- HERO IMAGE -->
  <div class="w-full overflow-hidden">
    <img
      src="https://images.metmuseum.org/CRDImages/ad/original/DP251139.jpg"
      alt="The Met Collection"
      class="w-full h-[650px] object-cover"
    />
  </div>

  <!-- CONTENT -->
  <div class="max-w-[1600px] mx-auto px-16 pt-14 pb-20">

    <h1 class="text-[72px] font-bold text-[#222] tracking-[-2px] leading-none">
      The Met Collection
    </h1>

    <p class="mt-10 text-[28px] leading-[1.7] text-[#333] max-w-[1200px] font-light">
      Explore more than 5,000 years of art from around the world. The Met
      Collection presents artworks, artifacts, and historical objects spanning
      cultures, civilizations, and artistic movements across history.
    </p>

  </div>

  <section class="w-full bg-[#f5f5f5] font-sans">

  <div class="max-w-[1550px] mx-auto px-16 py-20">

    <!-- TOP TEXT -->
    <h2 class="text-[58px] leading-[1.2] tracking-[-1.5px] text-[#111] font-normal max-w-[1250px]">
      Travel around the world and across 5,000 years of history through 490,000+ works of art.
    </h2>

    <!-- SEARCH -->
    <form action="{{ route('art.search') }}" method="GET" class="mt-16 flex items-center w-full border border-[#cfcfcf] bg-white h-[78px]">
      
      <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="Search The Collection"
        class="flex-1 h-full px-6 text-[22px] text-[#444] placeholder:text-[#9b9b9b] outline-none bg-transparent"
      />

      <button
        type="submit"
        class="w-[90px] h-full border-l border-[#cfcfcf] flex items-center justify-center hover:bg-[#f0f0f0] transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-7 h-7 text-[#666]"
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
    <div class="flex items-center gap-10 mt-12 flex-wrap">
      
      <a
        href="{{ route('art.search') }}"
        class="flex items-center gap-4 text-[20px] text-[#111] font-medium hover:underline"
      >
        Advanced Collection Search
        <span class="text-[30px] leading-none text-[#444]">›</span>
      </a>

      <a
        href="#"
        class="flex items-center gap-4 text-[20px] text-[#111] font-medium hover:underline"
      >
        Highlights
        <span class="text-[30px] leading-none text-[#444]">›</span>
      </a>

      <a
        href="#"
        class="flex items-center gap-4 text-[20px] text-[#111] font-medium hover:underline"
      >
        Public Domain Images
        <span class="text-[30px] leading-none text-[#444]">›</span>
      </a>

    </div>

    <!-- LINE -->
    <div class="w-full h-[1px] bg-[#dddddd] mt-16"></div>

    <!-- TITLE -->
    <h3 class="text-[64px] font-normal text-[#111] tracking-[-1.5px] mt-28 mb-14">
      Browse the Art
    </h3>

    <!-- GRID -->
    <div class="grid grid-cols-4 gap-x-8 gap-y-20">

      <!-- CARD 1 -->
      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/af/web-large/DP-14286-001.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          African Art in The Michael C. Rockefeller Wing
        </h4>

      </div>

      <!-- CARD 2 -->
      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/ad/web-large/DT1567.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          The American Wing
        </h4>

      </div>

      <!-- CARD 3 -->
      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/ao/web-large/DP251139.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          Ancient American Art in The Michael C. Rockefeller Wing
        </h4>

      </div>

      <!-- CARD 4 -->
      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/an/web-large/DT712.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          Ancient West Asian Art
        </h4>

      </div>

      <!-- SECOND ROW -->

      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/eg/web-large/DP354001.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          Egyptian Art
        </h4>

      </div>

      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/as/web-large/DP130155.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          Asian Art
        </h4>

      </div>

      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/es/web-large/DT1564.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          European Sculpture and Decorative Arts
        </h4>

      </div>

      <div class="group cursor-pointer">
        
        <div class="overflow-hidden">
          <img
            src="https://images.metmuseum.org/CRDImages/ep/web-large/DT1947.jpg"
            alt=""
            class="w-full h-[250px] object-cover group-hover:scale-[1.03] transition duration-500"
          />
        </div>

        <h4 class="text-[24px] leading-[1.25] text-[#111] mt-5 font-normal">
          European Paintings
        </h4>

      </div>

    </div>

  </div>

</section>


@endsection
