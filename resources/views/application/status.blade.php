@extends('layouts.applicant')

@section('content')
<div class="text-center py-12">
    @if($application->status === 'submitted' || $application->status === 'pending_review')
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900">Application Submitted!</h2>
        <p class="mt-4 text-lg text-gray-600">Your application has been successfully received and is under review.</p>
        
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg max-w-2xl mx-auto text-left">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Application Details</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Application Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-bold">{{ $application->application_number }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Submission Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->submitted_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending Review
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Programme</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->program->name ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('application.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Application Form
            </a>
        </div>
    @elseif($application->status === 'approved')
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-blue-100 mb-6">
            <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900">Congratulations!</h2>
        <p class="mt-4 text-lg text-gray-600">Your application has been approved.</p>
        <p class="mt-2 text-sm text-gray-500">Please check your email for admission letter and joining instructions.</p>
    @elseif($application->status === 'rejected')
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
            <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900">Application Update</h2>
        <p class="mt-4 text-lg text-gray-600">Your application was not successful.</p>
    @else
        <!-- Draft State -->
        <h2 class="text-2xl font-bold text-gray-900">Application in Progress</h2>
        <p class="mt-2 text-gray-600">You have an incomplete application.</p>
        <div class="mt-6">
            <a href="{{ route('application.step', ['step' => $application->current_step]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Continue Application (Step {{ $application->current_step }})
            </a>
        </div>
    @endif
</div>
@endsection
