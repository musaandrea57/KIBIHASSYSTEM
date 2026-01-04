<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KIBIHAS') }} - Student Registration</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .step-indicator {
            position: relative;
            z-index: 1;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #e5e7eb;
            z-index: -1;
            transform: translateY(-50%);
        }
        .step-item.active .step-circle {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }
        .step-item.completed .step-circle {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    <span class="ml-3 text-xl font-bold text-gray-800">KIBIHAS Admission Portal</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-600">Logged in as: <span class="font-semibold">{{ Auth::user()->email }}</span></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Already have an account? Log in</a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                @if(isset($step) && $step > 1)
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="step-indicator flex justify-between items-center">
                        @foreach(range(1, 8) as $s)
                            <div class="step-item flex flex-col items-center {{ $step == $s ? 'active' : ($step > $s ? 'completed' : '') }}">
                                <div class="step-circle w-8 h-8 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-sm font-semibold text-gray-500">
                                    @if($step > $s)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        {{ $s }}
                                    @endif
                                </div>
                                <span class="text-xs mt-1 font-medium text-gray-500 hidden sm:block">
                                    {{ ['Account', 'Personal', 'Contact', 'Academic', 'Program', 'Welfare', 'Docs', 'Finish'][$s-1] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
                    <div class="p-6 sm:p-10">
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            Please correct the errors below.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>

                <!-- Footer / Help -->
                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>Need help? Contact Admissions at <a href="mailto:admissions@kibihas.ac.tz" class="text-indigo-600 hover:underline">admissions@kibihas.ac.tz</a></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
