@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Accommodation') }}</h2>
        <p class="text-gray-600">View your hostel allocation and details.</p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @if($allocations->count() > 0)
                <h3 class="text-lg font-bold text-gray-800 mb-4">Current Allocation</h3>
                
                @foreach($allocations as $allocation)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-xl font-bold text-primary-700">{{ $allocation->hostel->name }}</h4>
                                <p class="text-gray-600">Room {{ $allocation->room->room_number }} ({{ ucfirst($allocation->room->room_type) }}) - Bed {{ $allocation->bed->bed_label }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $allocation->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ strtoupper($allocation->status) }}
                            </span>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="block text-gray-500">Allocated Date</span>
                                <span class="font-medium">{{ $allocation->allocated_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500">Session</span>
                                <span class="font-medium">{{ $allocation->academicYear->name }} - {{ $allocation->semester->name }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500">Check-in</span>
                                <span class="font-medium">{{ $allocation->check_in_date ? $allocation->check_in_date->format('d M Y') : 'Pending' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-500">
                                <span class="font-bold">Note:</span> Hostel fees are billed to your student finance account. Please ensure payment to avoid eviction.
                            </p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Accommodation Assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">You have not been allocated a room for this semester.</p>
                    <p class="mt-4 text-sm text-gray-500">Please visit the Welfare Office to apply for accommodation.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
