@props(['label', 'value', 'icon', 'color' => 'blue', 'subtext' => null, 'trend' => null])

@php
    $colors = [
        'blue' => 'bg-blue-50 text-blue-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'green' => 'bg-green-50 text-green-600',
        'violet' => 'bg-violet-50 text-violet-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'cyan' => 'bg-cyan-50 text-cyan-600',
        'red' => 'bg-red-50 text-red-600',
    ];
    $iconBg = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col justify-between hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-2">
        <div class="p-2 rounded-lg {{ $iconBg }}">
            @if($icon == 'users')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            @elseif($icon == 'academic-cap')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
            @elseif($icon == 'clock')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($icon == 'clipboard-check')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            @elseif($icon == 'currency-dollar')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($icon == 'user-plus')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            @endif
        </div>
        @if($trend)
            <span class="text-xs font-medium {{ $trend === 'active' || $trend === 'up' ? 'text-green-600 bg-green-50' : 'text-slate-500 bg-slate-50' }} px-2 py-0.5 rounded-full flex items-center">
                @if($trend === 'active') ↑ @endif
                @if($trend === 'neutral') - @endif
            </span>
        @endif
    </div>
    <div>
        <h3 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $value }}</h3>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mt-1">{{ $label }}</p>
        @if($subtext)
            <p class="text-xs text-slate-400 mt-1">{{ $subtext }}</p>
        @endif
    </div>
</div>
