@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Review & Submit</h2>
    <p class="mt-1 text-sm text-gray-600">Please review your information carefully before submitting. Once submitted, you cannot make changes.</p>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Applicant Information</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Personal details and application status.</p>
    </div>
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Full name</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->first_name }} {{ $application->last_name }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Application for</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $application->program->name ?? 'Not Selected' }} ({{ $application->study_mode }})
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Email address</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->email }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Phone number</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->phone }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Address</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $application->current_address }}, {{ $application->region }}, {{ $application->country }}
                </dd>
            </div>
             <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Academic Qualification</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                     {{ $application->education_background['highest_education'] ?? 'N/A' }} - {{ $application->education_background['secondary_school'] ?? 'N/A' }}
                </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Attachments</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                        @foreach($application->uploadedDocuments as $doc)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2 flex-1 w-0 truncate">
                                        {{ $doc->original_name }} ({{ strtoupper(str_replace('_', ' ', $doc->type)) }})
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="font-medium text-indigo-600 hover:text-indigo-500">Uploaded</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </dd>
            </div>
        </dl>
    </div>
</div>

<form method="POST" action="{{ route('application.step8.store') }}">
    @csrf
    
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    By submitting this application, you declare that all information provided is true and correct. False information may lead to disqualification or legal action.
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-start mb-6">
        <div class="flex items-center h-5">
            <input id="declaration" name="declaration" type="checkbox" required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="declaration" class="font-medium text-gray-700">I Agree to the Terms & Declaration</label>
            <p class="text-gray-500">I confirm that I have read and understood the admission requirements and regulations.</p>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 7]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Submit Application
        </button>
    </div>
</form>
@endsection
