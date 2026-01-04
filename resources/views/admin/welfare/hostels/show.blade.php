@extends('layouts.portal')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.welfare.hostels.index') }}" class="text-indigo-600 hover:underline">&larr; Back</a>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $hostel->name }}</h2>
            <p class="text-gray-600">Managing blocks and rooms.</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($hostel->blocks as $block)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-gray-800">{{ $block->name }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($hostel->rooms->where('block_id', $block->id) as $room)
                            <div class="border rounded p-3 {{ $room->available == 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                                <div class="font-bold text-gray-800 mb-1">Room {{ $room->room_number }}</div>
                                <div class="text-xs text-gray-500 mb-2">{{ ucfirst($room->room_type) }}</div>
                                
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($room->beds as $bed)
                                        <div class="text-xs text-center py-1 rounded {{ $bed->activeAllocation ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}" title="{{ $bed->activeAllocation ? $bed->activeAllocation->student->user->name : 'Available' }}">
                                            {{ $bed->bed_label }}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2 text-xs text-center font-bold {{ $room->available == 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $room->available }} / {{ $room->capacity }} Free
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Unblocked Rooms -->
        @if($hostel->rooms->whereNull('block_id')->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-gray-800">General Rooms</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($hostel->rooms->whereNull('block_id') as $room)
                            <div class="border rounded p-3 {{ $room->available == 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                                <div class="font-bold text-gray-800 mb-1">Room {{ $room->room_number }}</div>
                                <div class="text-xs text-gray-500 mb-2">{{ ucfirst($room->room_type) }}</div>
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($room->beds as $bed)
                                        <div class="text-xs text-center py-1 rounded {{ $bed->activeAllocation ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}" title="{{ $bed->activeAllocation ? $bed->activeAllocation->student->user->name : 'Available' }}">
                                            {{ $bed->bed_label }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
