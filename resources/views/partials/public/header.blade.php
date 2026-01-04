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
                <a href="{{ route('about') }}" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">About</a>
                <a href="{{ route('home') }}#programs" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Programs</a>
                <a href="{{ route('admission') }}" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Admissions</a>
                <a href="{{ route('campus-life') }}" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">Campus Life</a>
                <a href="{{ route('ict-services') }}" class="text-gray-500 hover:text-gray-900 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 transition duration-150 ease-in-out">ICT Services</a>
                
                @unless(request()->routeIs('login'))
                    <a href="{{ route('login') }}" class="bg-[#003366] hover:bg-[#002244] text-white font-bold py-2 px-5 rounded-full shadow-md transition transform hover:scale-105">
                        Portal Login
                    </a>
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
            <a href="{{ route('about') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">About</a>
            <a href="{{ route('home') }}#programs" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Programs</a>
            <a href="{{ route('admission') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Admissions</a>
            <a href="{{ route('campus-life') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">Campus Life</a>
            <a href="{{ route('ict-services') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">ICT Services</a>
        </div>
    </div>
</header>
