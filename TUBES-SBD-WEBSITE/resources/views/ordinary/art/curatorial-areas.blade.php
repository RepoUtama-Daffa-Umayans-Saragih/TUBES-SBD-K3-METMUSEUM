@extends('layouts.main')

@section('title', 'Curatorial Areas')

@section('content')

<!-- BREADCRUMB -->
    <div class="w-full border-b border-[#e5e5e5] bg-white">
    <div class="max-w-[1600px] mx-auto px-16 h-[70px] flex items-center gap-3">

      <!-- HOME -->
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

      <span class="text-[#777]">/</span>

     <a
        href="{{ route('about') }}"
        class="text-[14px] font-semibold text-black hover:underline"
      >
       About The Met
      </a>

      <span class="text-[#777]">/</span>

     <a
        href="#"
        class="text-[14px] font-semibold text-black"
      >
        Departments
      </a>

    </div>

  <!-- MAIN CONTENT -->
  <div class="max-w-[1560px] mx-auto px-16 pt-2 pb-19">

    <!-- TITLE -->
    <h1 class="text-[40px] tracking-[-2px] leading-none font-semibold text-[#111]">
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
    <div class="mt-10">

      <h2 class="text-[35px] tracking-[-1.5px] leading-none font-medium text-[#111]">
        Curatorial
      </h2>

      <!-- DESC -->
      <p class="mt-4 text-[20px] leading-[1.8] text-[#5c5c5c] font-light max-w-[1180px]">
        Curatorial departments study, exhibit, and care for over two million objects in
        <br />
        The Met Collection.
      </p>

      <!-- GRID -->
      <div class="mt-14">
        <x-department-navigation-grid :items="$departmentNavigationItems ?? []" />
      </div>
    </div>

@endsection
