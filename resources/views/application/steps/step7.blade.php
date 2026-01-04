@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Document Uploads</h2>
    <p class="mt-1 text-sm text-gray-600">Upload scanned copies of required documents. Files must be PDF, JPG, or PNG (Max 5MB each).</p>
</div>

@if ($errors->any())
    <div class="rounded-md bg-red-50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('application.step7.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="space-y-6">
        @php
            $docs = $application->uploadedDocuments->keyBy('type');
        @endphp

        <!-- Passport Photo -->
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <label for="passport_photo" class="text-lg font-medium leading-6 text-gray-900">Passport Size Photo</label>
                    <p class="text-sm text-gray-500">Recent color photo with blue background.</p>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6">
                    @if(isset($docs['passport_photo']))
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm font-medium">Uploaded: {{ $docs['passport_photo']->original_name }}</span>
                        </div>
                    @endif
                    <input type="file" name="passport_photo" id="passport_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>

        <!-- Birth Certificate -->
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <label for="birth_certificate" class="text-lg font-medium leading-6 text-gray-900">Birth Certificate</label>
                    <p class="text-sm text-gray-500">Certified copy of your birth certificate.</p>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6">
                    @if(isset($docs['birth_certificate']))
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm font-medium">Uploaded: {{ $docs['birth_certificate']->original_name }}</span>
                        </div>
                    @endif
                    <input type="file" name="birth_certificate" id="birth_certificate" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>

        <!-- Academic Certificates -->
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <label for="academic_certificate" class="text-lg font-medium leading-6 text-gray-900">Academic Certificates / Transcripts</label>
                    <p class="text-sm text-gray-500">O-Level / A-Level / College Certificates combined.</p>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6">
                    @if(isset($docs['academic_certificate']))
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm font-medium">Uploaded: {{ $docs['academic_certificate']->original_name }}</span>
                        </div>
                    @endif
                    <input type="file" name="academic_certificate" id="academic_certificate" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>

        <!-- NIDA ID -->
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <label for="nida_id" class="text-lg font-medium leading-6 text-gray-900">NIDA ID / Number</label>
                    <p class="text-sm text-gray-500">Copy of NIDA ID or Number Slip.</p>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6">
                    @if(isset($docs['nida_id']))
                        <div class="flex items-center text-green-600 mb-2">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm font-medium">Uploaded: {{ $docs['nida_id']->original_name }}</span>
                        </div>
                    @endif
                    <input type="file" name="nida_id" id="nida_id" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 6]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Review
        </button>
    </div>
</form>
@endsection
