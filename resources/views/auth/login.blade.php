<x-auth-layout>
    <div class="flex flex-col md:flex-row flex-grow">
        <!-- Left Panel (Brand / Trust) -->
        <div class="hidden md:flex md:w-1/2 bg-[#003366] text-white p-12 flex-col justify-center items-start relative overflow-hidden">
             <!-- Background Texture/Overlay -->
             <div class="absolute inset-0 bg-black opacity-10 pattern-grid-lg"></div>
             
             <!-- Content -->
             <div class="relative z-10 max-w-lg">
                 <img src="{{ asset('assets/brand/logo-full.svg') }}" class="h-28 mb-8 bg-white/10 rounded-lg p-2 backdrop-blur-sm" alt="KIBIHAS Logo">
                 
                 <h2 class="text-4xl font-serif font-bold mb-6 leading-tight text-white">
                     Welcome to the<br>
                     <span class="text-yellow-400">KIBIHAS Portal</span>
                 </h2>
                 
                 <p class="text-xl text-gray-200 mb-10 font-light border-l-4 border-yellow-500 pl-4">
                     Official Student & Staff Portal
                 </p>
                 
                 <div class="space-y-6 text-gray-200">
                     <div class="flex items-center space-x-4 group">
                         <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-yellow-500 group-hover:text-[#003366] transition-colors duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                         </div>
                         <span class="text-lg">Secure Access • Role-Based</span>
                     </div>
                     <div class="flex items-center space-x-4 group">
                         <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-yellow-500 group-hover:text-[#003366] transition-colors duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                         </div>
                         <span class="text-lg">Academic Results & Registration</span>
                     </div>
                     <div class="flex items-center space-x-4 group">
                         <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-yellow-500 group-hover:text-[#003366] transition-colors duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         </div>
                         <span class="text-lg">Fee Payments & Financials</span>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Right Panel (Login Card) -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 bg-gray-50">
            <div class="w-full max-w-md">
                 <!-- Mobile Logo (Visible only on mobile - REMOVED since it is now in the top panel) -->

                 <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8 border border-gray-100">
                     <div class="text-center mb-8">
                         <h2 class="text-2xl font-bold text-[#003366]">Sign in to Portal</h2>
                         <p class="text-sm text-gray-500 mt-2">Enter your credentials to access your account</p>
                     </div>

                     <!-- Session Status -->
                     <x-auth-session-status class="mb-4" :status="session('status')" />

                     <!-- Validation Errors -->
                     @if ($errors->any())
                        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-100 flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Login Failed</h3>
                                <div class="mt-1 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                     @endif

                     <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Identifier -->
                        <div>
                            <label for="identifier" class="block text-sm font-semibold text-gray-700 mb-1">Identifier</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <input id="identifier" name="email" type="text" value="{{ old('email') }}" required autofocus autocomplete="username"
                                    class="block w-full rounded-lg border-gray-300 pl-10 focus:border-[#003366] focus:ring-[#003366] sm:text-sm py-3 transition duration-150 ease-in-out"
                                    placeholder="Registration No / Staff No / Admin Username">
                            </div>
                            <p class="mt-2 text-xs text-gray-500 flex items-start">
                                <svg class="h-4 w-4 text-blue-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Students use Registration Number. Staff use Staff Number.
                            </p>
                        </div>

                        <!-- Password -->
                        <div x-data="{ show: false, capsLock: false }">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                    @keyup="capsLock = $event.getModifierState('CapsLock')"
                                    class="block w-full rounded-lg border-gray-300 pl-10 pr-10 focus:border-[#003366] focus:ring-[#003366] sm:text-sm py-3 transition duration-150 ease-in-out">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Toggle password visibility">
                                    <span x-show="!show">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </span>
                                    <span x-show="show" style="display: none;">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <p x-show="capsLock" style="display: none;" class="mt-2 text-xs text-yellow-600 font-medium flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Caps Lock is on
                            </p>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded cursor-pointer">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer select-none">Keep me signed in</label>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-[#003366] hover:text-blue-800 transition duration-150 ease-in-out">
                                        Forgot password?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" x-data="{ loading: false }" @click="loading = true" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-[#003366] hover:bg-[#002244] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#003366] transition duration-150 ease-in-out transform hover:-translate-y-0.5 disabled:opacity-75 disabled:cursor-not-allowed">
                                <span x-show="!loading" class="flex items-center">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Sign In
                                </span>
                                <span x-show="loading" style="display: none;" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing in...
                                </span>
                            </button>
                        </div>
                     </form>
                 </div>
                 
                 <div class="mt-6 text-center text-sm text-gray-500">
                     &copy; {{ date('Y') }} KIBIHAS. All rights reserved.
                 </div>
            </div>
        </div>
    </div>
</x-auth-layout>
