@props(['group'])

@php
    // Filter items based on permissions
    $visibleItems = collect($group['items'])->filter(function($item) {
        if (isset($item['permissions'])) {
            if (is_array($item['permissions'])) {
                foreach ($item['permissions'] as $permission) {
                    if (auth()->user()->can($permission)) {
                        return true;
                    }
                }
                return false;
            }
            return auth()->user()->can($item['permissions']);
        }
        return true; // No permissions defined means visible to all
    });
@endphp

@if($visibleItems->isNotEmpty())
    <div class="mb-2">
        @if(isset($group['heading']))
            <div 
                x-show="!collapsed" 
                class="px-4 mb-3 text-xs font-bold text-slate-400 uppercase tracking-wider transition-opacity duration-200"
            >
                {{ $group['heading'] }}
            </div>
            <!-- Collapsed state divider/indicator could go here if needed, but keeping it clean is better -->
            <div x-show="collapsed" class="px-2 mb-3 flex justify-center">
                <div class="h-px bg-gray-200 w-4/5"></div>
            </div>
        @endif

        <div class="space-y-1">
            @foreach($visibleItems as $item)
                <x-portal.sidebar-item :item="$item" />
            @endforeach
        </div>
    </div>
@endif
