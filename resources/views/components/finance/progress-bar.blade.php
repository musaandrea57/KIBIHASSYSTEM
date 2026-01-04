@props(['percentage', 'color' => 'blue'])

<div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
    <div class="bg-{{ $color }}-600 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
</div>
