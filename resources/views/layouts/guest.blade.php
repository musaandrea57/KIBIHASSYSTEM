<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIBIHAS') }} - Official Portal</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased flex flex-col min-h-screen bg-gray-50">
        <!-- Top Bar -->
        <div class="bg-[#003366] text-white py-2 px-4 text-xs font-medium tracking-wide">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <span>Official Student & Staff Portal</span>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-yellow-400 transition">Help Desk</a>
                    <a href="#" class="hover:text-yellow-400 transition">Staff Webmail</a>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center shrink-0 group">
                            <img src="{{ asset('assets/brand/logo-full.svg') }}" class="block h-12 w-auto mr-3 transition group-hover:opacity-90" alt="KIBIHAS Logo" />
                        </a>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex space-x-8 items-center ml-10">
                        @if(Route::has('home'))
                            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-nav-link>
                        @else
                            <a href="/" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Home</a>
                        @endif
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">About</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Programs</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Admissions</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Contact</a>
                        
                        @unless(request()->routeIs('login'))
                            <a href="{{ route('login') }}" class="bg-[#003366] hover:bg-[#002244] text-white font-bold py-2 px-5 rounded-full shadow-md transition transform hover:scale-105">
                                Portal Login
                            </a>
                        @else
                             <span class="bg-gray-100 text-[#003366] font-bold py-2 px-5 rounded-full border border-gray-200">
                                Portal Login
                            </span>
                        @endunless
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="-mr-2 flex items-center md:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" aria-label="Main menu">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-white border-t border-gray-200 shadow-lg">
                <div class="pt-2 pb-3 space-y-1">
                     @if(Route::has('home'))
                        <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-responsive-nav-link>
                     @else
                        <a href="/" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">Home</a>
                     @endif
                    <a href="#" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">About</a>
                    <a href="#" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Programs</a>
                    <a href="#" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Admissions</a>
                    <a href="#" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Contact</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-[#003366] text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center mb-4">
                            <img src="{{ asset('assets/brand/logo-full.svg') }}" class="h-10 w-auto mr-3 bg-white rounded-full p-1" alt="Logo">
                            <span class="font-serif font-bold text-xl text-yellow-500">KIBIHAS</span>
                        </div>
                        <p class="text-gray-300 text-sm mb-4 max-w-sm">
                            Kibosho Institute of Health and Allied Sciences is dedicated to training competent health professionals.
                        </p>
                    </div>
                    <div>
                         <h3 class="text-yellow-500 font-bold uppercase tracking-wider text-sm mb-3">Contact Us</h3>
                         <ul class="text-sm text-gray-300 space-y-2">
                             <li>P.O. Box 123, Moshi, Kilimanjaro</li>
                             <li>+255 123 456 789</li>
                             <li>info@kibihas.ac.tz</li>
                         </ul>
                    </div>
                    <div>
                         <h3 class="text-yellow-500 font-bold uppercase tracking-wider text-sm mb-3">Quick Links</h3>
                         <ul class="text-sm text-gray-300 space-y-2">
                             <li><a href="#" class="hover:text-white">Academic Calendar</a></li>
                             <li><a href="#" class="hover:text-white">Student Bylaws</a></li>
                             <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                         </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-gray-400 text-center md:text-left flex flex-col md:flex-row justify-between items-center">
                    <p>&copy; {{ date('Y') }} KIBIHAS. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Designed & Maintained by KIBIHAS ICT</p>
                </div>
            </div>
        </footer>
    </body>
</html>
