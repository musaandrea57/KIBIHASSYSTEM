@props(['item'])

@php
    $itemKey = Str::slug($item['label']);
    
    // Check active state
    $isActive = false;
    if (isset($item['route']) && Route::has($item['route'])) {
        $isActive = request()->routeIs($item['route']);
    }
    
    // Check children for active state
    if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
            if (isset($child['route']) && Route::has($child['route']) && request()->routeIs($child['route'])) {
                $isActive = true;
                break;
            }
        }
    }
    
    $hasChildren = !empty($item['children']);
@endphp

<div 
    x-data="{ 
        expanded: localStorage.getItem('sidebar_item_{{ $itemKey }}') === 'true' || {{ $isActive ? 'true' : 'false' }},
        hover: false,
        toggle() {
            if (!this.collapsed) {
                this.expanded = !this.expanded;
                localStorage.setItem('sidebar_item_{{ $itemKey }}', this.expanded);
            }
        }
    }" 
    @mouseenter="hover = true"
    @mouseleave="hover = false"
    class="relative group"
>
    <!-- Item Link/Button -->
    <a 
        href="{{ $hasChildren ? '#' : (isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#') }}"
        @if($hasChildren) @click.prevent="toggle()" @endif
        class="flex items-center px-3 py-2.5 text-sm font-medium transition-all duration-200 rounded-md group-hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
        :class="{ 
            'bg-blue-50 text-blue-800': {{ $isActive ? 'true' : 'false' }},
            'text-slate-600 hover:text-blue-800': !{{ $isActive ? 'true' : 'false' }},
            'justify-center': collapsed,
            'justify-between': !collapsed
        }"
        :title="collapsed ? '{{ $item['label'] }}' : ''"
    >
        <!-- Active Indicator (Left Border) -->
        @if($isActive)
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-r-md"></div>
        @endif

        <div class="flex items-center min-w-0">
            @if(isset($item['icon']))
                <x-portal.icon 
                    name="{{ $item['icon'] }}" 
                    class="w-5 h-5 flex-shrink-0 transition-colors duration-200" 
                    x-bind:class="{ 
                        'text-blue-800': {{ $isActive ? 'true' : 'false' }} || (collapsed && hover),
                        'text-slate-400 group-hover:text-blue-800': !{{ $isActive ? 'true' : 'false' }},
                        'mr-3': !collapsed
                    }" 
                />
            @endif
            <span x-show="!collapsed" class="truncate origin-left duration-200">{{ $item['label'] }}</span>
        </div>

        @if($hasChildren)
            <div x-show="!collapsed">
                <svg 
                    class="w-4 h-4 text-slate-400 transition-transform duration-200" 
                    :class="{ 'rotate-180': expanded, 'text-blue-800': {{ $isActive ? 'true' : 'false' }} }" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        @endif
    </a>

    <!-- Inline Children (Expanded & Not Collapsed) -->
    @if($hasChildren)
        <div 
            x-show="expanded && !collapsed" 
            x-collapse 
            class="mt-1 space-y-1 pl-10 overflow-hidden"
        >
            @foreach($item['children'] as $child)
                @if(isset($child['permissions']) && !auth()->user()->can($child['permissions']))
                    @continue
                @endif
                
                @if(isset($child['route']) && Route::has($child['route']))
                    <a 
                        href="{{ route($child['route']) }}" 
                        class="block px-3 py-2 text-sm rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 {{ request()->routeIs($child['route']) ? 'text-blue-800 font-medium bg-blue-50' : 'text-slate-500 hover:text-blue-800 hover:bg-blue-50' }}"
                    >
                        {{ $child['label'] }}
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Flyout Panel (Collapsed & Hover) -->
        <div 
            x-show="collapsed" 
            class="hidden group-hover:block absolute left-full top-0 ml-2 w-56 bg-white border border-gray-200 shadow-xl rounded-md overflow-hidden z-50"
        >
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-sm font-bold text-slate-800">{{ $item['label'] }}</p>
            </div>
            <div class="py-1 bg-white">
                @foreach($item['children'] as $child)
                    @if(isset($child['permissions']) && !auth()->user()->can($child['permissions']))
                        @continue
                    @endif

                    @if(isset($child['route']) && Route::has($child['route']))
                        <a 
                            href="{{ route($child['route']) }}" 
                            class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-800 transition-colors {{ request()->routeIs($child['route']) ? 'bg-blue-50 text-blue-800 font-medium' : '' }}"
                        >
                            {{ $child['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
