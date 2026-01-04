<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIBIHAS') }} Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js Persist (if not in bundle, we handle manually, but assuming standard Laravel Breeze setup includes Alpine) -->
        <style>
            [x-cloak] { display: none !important; }
            /* Custom Scrollbar for Sidebar */
            .sidebar-scrollbar::-webkit-scrollbar {
                width: 5px;
            }
            .sidebar-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .sidebar-scrollbar::-webkit-scrollbar-thumb {
                background-color: #334155;
                border-radius: 20px;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-slate-900">
        <div 
            x-data="{ 
                mobileOpen: false, 
                collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebar_collapsed', this.collapsed);
                }
            }" 
            class="flex h-screen overflow-hidden"
        >
            <!-- Sidebar Component -->
            <x-portal.sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Header -->
                <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 shrink-0">
                    <div class="flex items-center">
                        <!-- Mobile Hamburger -->
                        <button 
                            @click="mobileOpen = true" 
                            class="text-slate-500 hover:text-slate-700 focus:outline-none lg:hidden mr-4"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Desktop Collapse Toggle -->
                        <button 
                            @click="toggleCollapse()" 
                            class="hidden lg:flex items-center justify-center w-8 h-8 rounded-full hover:bg-slate-100 text-slate-500 transition-colors"
                            :title="collapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                        >
                            <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': collapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Header Right Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications (Placeholder) -->
                        <button class="p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors relative">
                            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>
                    </div>
                </header>

                <!-- Main Content Scroll Area -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 px-4 sm:px-6 lg:px-8 py-6">
                    <div class="max-w-7xl mx-auto">
                        @if (isset($slot) && $slot->isNotEmpty())
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
