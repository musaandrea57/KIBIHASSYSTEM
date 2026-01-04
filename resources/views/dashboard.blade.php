@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applicant Dashboard') }}
        </h2></div>

    @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @php
                $application = auth()->user()->applications->first();
            @endphp

            @if($application)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-primary-900 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-2xl font-bold">Application Status</h3>
                                <p class="text-primary-200">Ref: {{ $application->application_number }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusBadgeClass = match($application->status) {
                                        'submitted' => implode(' ', ['bg-blue-500', 'text-white']),
                                        'under_review' => implode(' ', ['bg-yellow-500', 'text-black']),
                                        'approved' => implode(' ', ['bg-green-500', 'text-white']),
                                        'rejected' => implode(' ', ['bg-red-500', 'text-white']),
                                        default => implode(' ', ['bg-gray-500', 'text-white']),
                                    };
                                @endphp
                                <span class="inline-block px-4 py-2 rounded-full font-bold text-sm uppercase {{ $statusBadgeClass }}">
                                    {{ str_replace('_', ' ', $application->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-b border-gray-200">
                        <!-- Progress Bar -->
                        <div class="relative py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-1/4 text-center">
                                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ in_array($application->status, ['submitted', 'under_review', 'approved', 'admitted']) ? 'bg-primary-700 text-white' : 'bg-gray-300 text-gray-500' }}">1</div>
                                    <div class="text-xs mt-1 font-bold">Submitted</div>
                                    <div class="text-xs text-gray-500">{{ $application->created_at->format('d M Y') }}</div>
                                </div>
                                <div class="w-1/4 text-center">
                                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ in_array($application->status, ['under_review', 'approved', 'admitted']) ? 'bg-primary-700 text-white' : 'bg-gray-300 text-gray-500' }}">2</div>
                                    <div class="text-xs mt-1 font-bold">Under Review</div>
                                </div>
                                <div class="w-1/4 text-center">
                                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ in_array($application->status, ['approved', 'admitted']) ? 'bg-primary-700 text-white' : 'bg-gray-300 text-gray-500' }}">3</div>
                                    <div class="text-xs mt-1 font-bold">Outcome</div>
                                </div>
                                <div class="w-1/4 text-center">
                                    <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center {{ in_array($application->status, ['admitted']) ? 'bg-primary-700 text-white' : 'bg-gray-300 text-gray-500' }}">4</div>
                                    <div class="text-xs mt-1 font-bold">Admitted</div>
                                </div>
                            </div>
                            <div class="absolute top-8 left-0 w-full h-1 bg-gray-200 -z-10">
                                <div class="h-full bg-primary-700 transition-all duration-500" style="width: 
                                    @if($application->status == 'submitted') 15% 
                                    @elseif($application->status == 'under_review') 40% 
                                    @elseif($application->status == 'approved') 65% 
                                    @elseif($application->status == 'admitted') 100% 
                                    @else 15% @endif">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h4 class="font-bold text-lg mb-4">Application Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Applicant Name</p>
                                <p class="font-medium">{{ $application->first_name }} {{ $application->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Program Applied</p>
                                <p class="font-medium">{{ $application->program->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $application->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ $application->phone }}</p>
                            </div>
                        </div>
                        
                        @if($application->status == 'approved')
                            <div class="mt-8 p-4 bg-green-50 border border-green-200 rounded-md">
                                <h5 class="font-bold text-green-800 mb-2">Congratulations!</h5>
                                <p class="text-green-700">Your application has been approved. You have been admitted to the {{ $application->program->name }} program.</p>
                                <p class="mt-2 text-green-700">Please visit the campus for registration.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Active Application Found</h3>
                        <p class="text-gray-600 mb-6">You haven't submitted an application yet.</p>
                        <a href="{{ route('application.register') }}" class="inline-block bg-secondary text-primary-900 font-bold py-3 px-8 rounded hover:bg-secondary-600 transition">
                            Start New Application
                        </a>
                    </div>
                </div>
            @endif
        @endsection
