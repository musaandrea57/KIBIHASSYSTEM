<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if(isset($urgentAnnouncements) && $urgentAnnouncements->isNotEmpty())
                    <div class="max-w-7xl mx-auto pt-6 sm:px-6 lg:px-8">
                        @foreach($urgentAnnouncements as $announcement)
                            <div class="bg-red-50 text-red-700 px-4 py-3 rounded shadow-sm mb-4 border-l-4 border-red-500 relative" role="alert">
                                <div class="flex items-start">
                                    <div class="py-1">
                                        <svg class="h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">{{ $announcement->title }}</p>
                                        <div class="text-sm mt-1">{!! \Illuminate\Support\Str::limit(strip_tags($announcement->content), 200) !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (isset($slot) && $slot->isNotEmpty())
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </body>
</html>
