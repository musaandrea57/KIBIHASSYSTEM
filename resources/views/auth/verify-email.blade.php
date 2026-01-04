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
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div>
                            <x-primary-button>
                                {{ __('Resend Verification Email') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
