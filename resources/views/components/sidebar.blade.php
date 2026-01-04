<div 
    x-data="{ mobileOpen: false }" 
    class="flex h-screen overflow-hidden bg-gray-100"
>
    <!-- Mobile sidebar backdrop -->
    <div 
        x-show="mobileOpen" 
        @click="mobileOpen = false" 
        x-transition:enter="transition-opacity ease-linear duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition-opacity ease-linear duration-300" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <div 
        :class="mobileOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" 
        class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-blue-900 lg:translate-x-0 lg:static lg:inset-0"
    >
        <div class="flex items-center justify-center mt-8">
            <div class="flex items-center">
                <span class="mx-2 text-2xl font-semibold text-white">KIBIHAS</span>
            </div>
        </div>

        <nav class="mt-10">
            @php
                $user = Auth::user();
                $menu = [];
                if ($user) {
                    $role = $user->roles->first()->name ?? 'guest';
                     // Fallback for roles that might not be exactly matching config keys or multiple roles
                    if ($user->hasRole('admin')) $role = 'admin';
                    elseif ($user->hasRole('academic_staff')) $role = 'academic_staff';
                    elseif ($user->hasRole('teacher')) $role = 'teacher';
                    elseif ($user->hasRole('student')) $role = 'student';
                    elseif ($user->hasRole('applicant')) $role = 'applicant';
                    elseif ($user->hasRole('parent')) $role = 'parent';
                    elseif ($user->hasRole('principal')) $role = 'principal';
                    elseif ($user->hasRole('accountant')) $role = 'accountant';

                    $menu = config("sidebar.menu.{$role}", []);
                }
            @endphp

            @foreach($menu as $item)
                @if(isset($item['header']))
                    <div class="px-6 py-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        {{ $item['header'] }}
                    </div>
                @else
                    <x-sidebar-item 
                        :label="$item['label']" 
                        :route="$item['route'] ?? null" 
                        :icon="$item['icon'] ?? null" 
                        :children="$item['children'] ?? []"
                    />
                @endif
            @endforeach
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-blue-900">
            <div class="flex items-center">
                <button @click="mobileOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div class="flex items-center">
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                        <img class="object-cover w-full h-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0D47A1&color=fff" alt="Your avatar">
                    </button>

                    <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                    <div x-show="dropdownOpen" class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl" style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-900 hover:text-white">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-900 hover:text-white">Logout</a>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Body -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
            <div class="container px-6 py-8 mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
