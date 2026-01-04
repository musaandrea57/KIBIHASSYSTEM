@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hostel Occupancy Report') }}
        </h2></div>

    <div class="space-y-6"><!-- Occupancy Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Occupancy Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($occupancy as $stat)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h4 class="font-bold text-lg">{{ $stat['name'] }}</h4>
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Beds:</span>
                                        <span class="font-medium">{{ $stat['total_beds'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Occupied:</span>
                                        <span class="font-medium text-blue-600">{{ $stat['occupied'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Available:</span>
                                        <span class="font-medium text-green-600">{{ $stat['available'] }}</span>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Occupancy Rate:</span>
                                            <span class="font-bold {{ $stat['occupancy_rate'] > 90 ? 'text-red-600' : 'text-gray-800' }}">
                                                {{ $stat['occupancy_rate'] }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $stat['occupancy_rate'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Allocations List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Allocations</h3>
                        <!-- Add Export Button Here if needed -->
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel / Room</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allocated At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allocations as $allocation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $allocation->student->first_name }} {{ $allocation->student->last_name }}
                                            <div class="text-xs text-gray-500">{{ $allocation->student->admission_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $allocation->hostel->name }}
                                            <div class="text-xs text-gray-500">{{ $allocation->room->room_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $allocation->bed->bed_label }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $allocation->allocated_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $allocation->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($allocation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $allocations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection