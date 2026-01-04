@extends('layouts.portal')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Hostel Management') }}</h2>
            <p class="text-gray-600">Manage hostels, blocks, and rooms.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.welfare.hostels.allocations') }}" class="px-4 py-2 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">View Allocations</a>
            <!-- Create Hostel Button (Optional) -->
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($hostels as $hostel)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-xl font-bold text-gray-800">{{ $hostel->name }}</h3>
                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $hostel->code }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="mr-4">{{ ucfirst($hostel->gender) }} Only</span>
                        <span>{{ $hostel->rooms_count }} Rooms</span>
                    </div>
                    <a href="{{ route('admin.welfare.hostels.show', $hostel->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 font-bold">
                        Manage Details
                    </a>
                </div>
            </div>
        @endforeach
    </div>

@endsection
