@extends('layouts.main')

@section('title', 'Curatorial Areas')

@section('content')
     <!-- MAIN CONTENT -->
  <div class="max-w-[1560px] mx-auto px-16 pt-14 pb-24">

    <!-- BREADCRUMB -->
    <div class="flex items-center gap-4 text-[18px] text-[#222]">

      <!-- HOME -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-[15px] h-[15px]"
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

      <span class="text-[#777]">/</span>

      <a href="#" class="text-[#333] hover:underline">
        About The Met
      </a>

      <span class="text-[#777]">/</span>

      <span class="font-medium">
        Departments
      </span>

    </div>

    <!-- TITLE -->
    <h1 class="mt-18 text-[48px] tracking-[-2px] leading-none font-semibold text-[#111]">
      Departments
    </h1>

    <!-- LINE -->
    <div class="w-full h-[1px] bg-[#dddddd] mt-16"></div>

    <!-- JUMP -->
    <div class="flex items-center gap-12 mt-12 text-[15px]">

      <span class="text-[#666]">
        Jump to:
      </span>

      <a href="#" class="font-semibold text-[#111]">
        Curatorial
      </a>

      <a href="#" class="text-[#111] hover:underline">
        Conservation and Scientific Research
      </a>

    </div>

    <!-- CURATORIAL -->
    <div class="mt-32">

      <h2 class="text-[35px] tracking-[-1.5px] leading-none font-medium text-[#111]">
        Curatorial
      </h2>

      <!-- DESC -->
      <p class="mt-10 text-[27px] leading-[1.8] text-[#5c5c5c] font-light max-w-[1180px]">
        Curatorial departments study, exhibit, and care for over two million objects in
        <br />
        The Met Collection.
      </p>

    <!-- GRID -->
<div class="grid grid-cols-4 gap-x-8 gap-y-24 mt-14">

  <!-- CARD 1 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/af/web-large/DP-14286-001.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      African Art in The Michael C.
      <br />
      Rockefeller Wing
    </h3>

  </div>

  <!-- CARD 2 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/ad/web-large/DT1567.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      The American Wing
    </h3>

  </div>

  <!-- CARD 3 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/ao/web-large/DP251139.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Ancient American Art in The
      <br />
      Michael C. Rockefeller Wing
    </h3>

  </div>

  <!-- CARD 4 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/an/web-large/DT712.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Ancient West Asian Art
    </h3>

  </div>

  <!-- CARD 5 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/aa/web-large/DT1055.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Arms and Armor
    </h3>

  </div>

  <!-- CARD 6 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/as/web-large/DP130155.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Asian Art
    </h3>

  </div>

  <!-- CARD 7 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/ci/web-large/DP341005.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      The Costume Institute
    </h3>

  </div>

  <!-- CARD 8 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/dp/web-large/DP807166.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Drawings and Prints
    </h3>

  </div>

  <!-- CARD 9 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/eg/web-large/DT250341.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Egyptian Art
    </h3>

  </div>

  <!-- CARD 10 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/ep/web-large/DT1947.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      European Paintings
    </h3>

  </div>

  <!-- CARD 11 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/es/web-large/DT1564.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      European Sculpture and
      <br />
      Decorative Arts
    </h3>

  </div>

  <!-- CARD 12 -->
  <div class="group cursor-pointer">

    <div class="w-full h-[275px] overflow-hidden bg-[#ddd]">
      <img
        src="https://images.metmuseum.org/CRDImages/gr/web-large/DP146636.jpg"
        alt=""
        class="w-full h-full object-cover"
      />
    </div>

    <h3 class="mt-5 text-[25px] leading-[1.22] tracking-[-0.4px] font-normal text-[#111]">
      Greek and Roman Art
    </h3>

  </div>

</div>

@endsection
