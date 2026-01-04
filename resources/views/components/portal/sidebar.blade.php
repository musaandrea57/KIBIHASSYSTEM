<div 
    :class="[
        mobileOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in',
        collapsed ? 'w-[88px]' : 'w-72'
    ]" 
    class="fixed inset-y-0 left-0 z-30 flex flex-col transition-all duration-300 bg-white border-r border-gray-200 shadow-lg lg:translate-x-0 lg:static lg:inset-0 lg:shadow-none"
    x-cloak
>
    <!-- Sticky Brand Header -->
    <div class="flex items-center justify-center h-16 bg-white border-b border-gray-200 shrink-0">
        <div class="flex items-center space-x-3 overflow-hidden" :class="{ 'px-6 w-full': !collapsed, 'px-2 justify-center': collapsed }">
            <!-- Logo Icon -->
            <div class="flex items-center justify-center shrink-0 w-8 h-8 bg-blue-700 rounded-lg shadow-sm">
                <span class="text-lg font-bold text-white">K</span>
            </div>
            <!-- Brand Name -->
            <span x-show="!collapsed" class="text-xl font-bold tracking-tight text-blue-900 truncate transition-opacity duration-200">
                KIBIHAS
            </span>
        </div>
    </div>

    <!-- Scrollable Menu List -->
    <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto sidebar-scrollbar">
        @foreach($menuGroups as $group)
            <x-portal.sidebar-group :group="$group" />
        @endforeach
    </nav>

    <!-- Sticky Profile Footer -->
    <div 
        class="border-t border-gray-200 bg-gray-50 shrink-0"
        x-data="{ profileOpen: false }"
    >
        <div class="relative">
            <!-- Profile Button -->
            <button 
                @click="profileOpen = !profileOpen"
                class="flex items-center w-full p-4 transition-colors hover:bg-gray-100 focus:outline-none"
                :class="{ 'justify-center': collapsed }"
            >
                <img 
                    class="object-cover w-9 h-9 border-2 border-white rounded-full shadow-sm shrink-0" 
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D47A1&color=fff" 
                    alt="{{ Auth::user()->name }}"
                >
                
                <div x-show="!collapsed" class="ml-3 text-left overflow-hidden transition-opacity duration-200">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs font-medium text-slate-500 truncate">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                </div>

                <!-- Chevron -->
                <div x-show="!collapsed" class="ml-auto text-slate-400">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="profileOpen" 
                @click.away="profileOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                class="absolute bottom-full left-0 w-full mb-1 bg-white border border-gray-200 shadow-lg rounded-t-lg overflow-hidden z-50"
                :class="{ 'w-64 left-0 ml-2 mb-2 rounded-lg': collapsed }"
                style="display: none;"
            >
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700">
                        <x-portal.icon name="user" class="w-4 h-4 mr-3" />
                        Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <x-portal.icon name="arrow-left-on-rectangle" class="w-4 h-4 mr-3" />
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
