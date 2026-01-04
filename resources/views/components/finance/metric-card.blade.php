@props(['title', 'amount', 'icon', 'color' => 'blue'])

<div class="bg-white rounded-lg shadow-sm border-l-4 border-{{ $color }}-600 p-6 flex items-start justify-between transition-transform hover:-translate-y-1 duration-200 h-full">
    <div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $title }}</p>
        <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($amount, 2) }} <span class="text-xs text-gray-400 font-normal">TZS</span></h3>
    </div>
    <div class="p-3 bg-{{ $color }}-50 rounded-full text-{{ $color }}-600">
        {{ $slot }}
    </div>
</div>
