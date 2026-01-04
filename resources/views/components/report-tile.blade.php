@props(['title', 'icon', 'color' => 'blue', 'url' => '#'])

@php
    $colors = [
        'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100',
        'indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
        'green' => 'bg-green-50 text-green-700 hover:bg-green-100',
        'violet' => 'bg-violet-50 text-violet-700 hover:bg-violet-100',
    ];
    $classes = $colors[$color] ?? $colors['blue'];
@endphp

<a href="{{ $url }}" class="flex items-center p-4 rounded-xl border border-transparent transition-all {{ $classes }}">
    <div class="mr-4 p-2 bg-white rounded-lg shadow-sm text-opacity-80">
        @if($icon == 'chart-bar')
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        @elseif($icon == 'user-group')
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        @elseif($icon == 'banknotes')
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        @endif
    </div>
    <div>
        <h5 class="font-bold text-sm">{{ $title }}</h5>
        <span class="text-xs opacity-75">Download PDF</span>
    </div>
</a>
