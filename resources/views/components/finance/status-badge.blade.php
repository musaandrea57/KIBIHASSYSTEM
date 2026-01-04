@props(['status'])

@php
    $normalizedStatus = ucfirst(strtolower($status));
    $colors = [
        'Paid' => 'bg-green-100 text-green-800 border border-green-200',
        'Posted' => 'bg-green-100 text-green-800 border border-green-200',
        'Partial' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'Unpaid' => 'bg-red-100 text-red-800 border border-red-200',
        'Overdue' => 'bg-red-100 text-red-800 border border-red-200 font-bold',
        'Pending' => 'bg-gray-100 text-gray-600 border border-gray-200',
        'Issued' => 'bg-blue-100 text-blue-800 border border-blue-200',
        'Voided' => 'bg-gray-200 text-gray-500 border border-gray-300 decoration-line-through',
    ];
    $classes = $colors[$normalizedStatus] ?? 'bg-gray-100 text-gray-800';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ $normalizedStatus }}
</span>
