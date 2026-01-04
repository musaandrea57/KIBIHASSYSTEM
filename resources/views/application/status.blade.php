@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Application Dashboard') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8 text-center">
                        <h2 class="text-2xl font-bold text-gray-900">Application Status</h2>
                        <p class="text-gray-500">Application Number: <span class="font-mono font-bold text-primary-600">{{ $application->application_number }}</span></p>
                    </div>

                    <!-- Status Timeline -->
                    <div class="relative py-8">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-200">
                        <div class="relative flex justify-between">
                            @php
                                $statuses = ['submitted', 'under_review', 'approved', 'admitted'];
                                $currentStatus = $application->status;
                                $statusIndex = array_search($currentStatus, $statuses);
                                if ($statusIndex === false && $currentStatus == 'rejected') $statusIndex = -1;
                            @endphp

                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center {{ $index <= $statusIndex ? 'bg-green-600' : 'bg-gray-200' }}">
                                        @if($index <= $statusIndex)
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-xs text-gray-500 font-bold">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-sm font-medium {{ $index <= $statusIndex ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($application->status == 'rejected')
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Your application has been rejected. Please contact the admission office for more details.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Application Details -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->email }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->phone }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Program</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->program->name }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Documents</h3>
                            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                @foreach($application->documents as $type => $path)
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-2 flex-1 w-0 truncate">
                                                {{ ucwords(str_replace('_', ' ', $type)) }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ Storage::url($path) }}" target="_blank" class="font-medium text-primary-600 hover:text-primary-500">
                                                View
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection