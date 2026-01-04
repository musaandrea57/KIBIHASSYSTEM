@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Programme & Study Details</h2>
    <p class="mt-1 text-sm text-gray-600">Select the program you wish to study and your preferred mode of study.</p>
</div>

<form method="POST" action="{{ route('application.step5.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="program_id" class="block text-sm font-medium text-gray-700">Select Programme</label>
            <div class="mt-1">
                <select id="program_id" name="program_id" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Choose a Programme</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', $application->program_id) == $program->id ? 'selected' : '' }}>
                            {{ $program->name }} ({{ $program->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            @error('program_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
            <div class="mt-1">
                <select id="academic_year_id" name="academic_year_id" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Year</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ old('academic_year_id', $application->academic_year_id) == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('academic_year_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="intake" class="block text-sm font-medium text-gray-700">Intake</label>
            <div class="mt-1">
                <select id="intake" name="intake" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Intake</option>
                    <option value="March" {{ old('intake', $application->intake) == 'March' ? 'selected' : '' }}>March</option>
                    <option value="September" {{ old('intake', $application->intake) == 'September' ? 'selected' : '' }}>September</option>
                </select>
            </div>
            @error('intake') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="study_mode" class="block text-sm font-medium text-gray-700">Study Mode</label>
            <div class="mt-1">
                <select id="study_mode" name="study_mode" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Mode</option>
                    <option value="Full-time" {{ old('study_mode', $application->study_mode) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="Part-time" {{ old('study_mode', $application->study_mode) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                </select>
            </div>
            @error('study_mode') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="sponsorship" class="block text-sm font-medium text-gray-700">Sponsorship Status</label>
            <div class="mt-1">
                <select id="sponsorship" name="sponsorship" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Sponsorship</option>
                    <option value="Private" {{ old('sponsorship', $application->sponsorship) == 'Private' ? 'selected' : '' }}>Private (Self-Sponsored)</option>
                    <option value="Government" {{ old('sponsorship', $application->sponsorship) == 'Government' ? 'selected' : '' }}>Government Sponsored</option>
                    <option value="Employer" {{ old('sponsorship', $application->sponsorship) == 'Employer' ? 'selected' : '' }}>Employer Sponsored</option>
                </select>
            </div>
            @error('sponsorship') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 4]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Continue
        </button>
    </div>
</form>
@endsection
