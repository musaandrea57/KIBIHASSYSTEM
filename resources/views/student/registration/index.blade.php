@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semester Registration') }}
        </h2></div>

    <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <!-- Active Registration Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Session Status</h3>
                    
                    @if($deadline)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Registration Period:</p>
                            <p class="font-semibold">{{ $deadline->academicYear->name }} - {{ $deadline->semester->name }}</p>
                            <p class="text-xs text-gray-500">Ends: {{ $deadline->end_date->format('M d, Y') }}</p>
                        </div>

                        @if($currentRegistration)
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-md">
                                <div>
                                    <span class="text-gray-600">Status:</span>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $currentRegistration->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($currentRegistration->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($currentRegistration->status) }}
                                    </span>
                                </div>
                                <a href="{{ route('student.registration.show', $currentRegistration) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View Details</a>
                            </div>
                        @else
                            <div class="mt-4">
                                <a href="{{ route('student.registration.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Register Now
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 italic">No active registration period at this time.</p>
                    @endif

            <!-- History Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Registration History</h3>
                        <a href="{{ route('student.registration.history') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View All</a>
                    </div>
                    
                    @if($history->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($history->take(3) as $reg)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reg->academicYear->name }} - {{ $reg->semester->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $reg->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $reg->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                       ($reg->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($reg->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('student.registration.show', $reg) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No past registrations found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection