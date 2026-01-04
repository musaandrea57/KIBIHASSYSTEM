@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Academic Background</h2>
    <p class="mt-1 text-sm text-gray-600">Enter details of your previous education (O-Level, A-Level, or College).</p>
</div>

<form method="POST" action="{{ route('application.step4.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="highest_education" class="block text-sm font-medium text-gray-700">Highest Education Level Attained</label>
            <div class="mt-1">
                <select id="highest_education" name="highest_education" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Level</option>
                    <option value="CSEE (Form IV)" {{ old('highest_education', $application->education_background['highest_education'] ?? '') == 'CSEE (Form IV)' ? 'selected' : '' }}>CSEE (Form IV)</option>
                    <option value="ACSEE (Form VI)" {{ old('highest_education', $application->education_background['highest_education'] ?? '') == 'ACSEE (Form VI)' ? 'selected' : '' }}>ACSEE (Form VI)</option>
                    <option value="Certificate" {{ old('highest_education', $application->education_background['highest_education'] ?? '') == 'Certificate' ? 'selected' : '' }}>Certificate</option>
                    <option value="Diploma" {{ old('highest_education', $application->education_background['highest_education'] ?? '') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                </select>
            </div>
            @error('highest_education') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="secondary_school" class="block text-sm font-medium text-gray-700">School/College Name</label>
            <div class="mt-1">
                <input type="text" name="secondary_school" id="secondary_school" required value="{{ old('secondary_school', $application->education_background['secondary_school'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('secondary_school') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="completion_year" class="block text-sm font-medium text-gray-700">Year of Completion</label>
            <div class="mt-1">
                <input type="number" name="completion_year" id="completion_year" required min="1990" max="{{ date('Y') }}" value="{{ old('completion_year', $application->education_background['completion_year'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('completion_year') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="exam_body" class="block text-sm font-medium text-gray-700">Examination Body</label>
            <div class="mt-1">
                <select id="exam_body" name="exam_body" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Body</option>
                    <option value="NECTA" {{ old('exam_body', $application->education_background['exam_body'] ?? '') == 'NECTA' ? 'selected' : '' }}>NECTA</option>
                    <option value="NACTVET" {{ old('exam_body', $application->education_background['exam_body'] ?? '') == 'NACTVET' ? 'selected' : '' }}>NACTVET</option>
                    <option value="Foreign" {{ old('exam_body', $application->education_background['exam_body'] ?? '') == 'Foreign' ? 'selected' : '' }}>Foreign Board</option>
                </select>
            </div>
            @error('exam_body') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="index_number" class="block text-sm font-medium text-gray-700">Index/Registration Number</label>
            <div class="mt-1">
                <input type="text" name="index_number" id="index_number" required placeholder="e.g. S0101/0001/2020" value="{{ old('index_number', $application->education_background['index_number'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('index_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        
        <div class="sm:col-span-2">
            <label for="previous_institution" class="block text-sm font-medium text-gray-700">Previous Institution (if transferring)</label>
            <div class="mt-1">
                <input type="text" name="previous_institution" id="previous_institution" value="{{ old('previous_institution', $application->education_background['previous_institution'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('previous_institution') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 3]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Continue
        </button>
    </div>
</form>
@endsection
