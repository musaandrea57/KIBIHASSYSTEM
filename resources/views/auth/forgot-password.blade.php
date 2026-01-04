<x-auth-layout>
    <div class="flex flex-col justify-center items-center flex-grow bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 text-gray-200 pattern-grid-lg opacity-50"></div>
        
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden relative z-10 border border-gray-100">
            <div class="bg-[#003366] py-4 px-6 flex justify-center items-center">
                 <img src="{{ asset('assets/brand/logo-full.svg') }}" class="h-12 bg-white/10 rounded p-1 backdrop-blur-sm" alt="Logo">
            </div>
            <div class="p-8">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-auth-layout>
