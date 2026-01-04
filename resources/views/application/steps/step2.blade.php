@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Personal & Identity Information</h2>
    <p class="mt-1 text-sm text-gray-600">Please provide your personal details as they appear on your official documents.</p>
</div>

<form method="POST" action="{{ route('application.step2.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
            <div class="mt-1">
                <select id="gender" name="gender" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $application->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $application->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            @error('gender') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <div class="mt-1">
                <input type="date" name="dob" id="dob" required value="{{ old('dob', $application->dob ? $application->dob->format('Y-m-d') : '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('dob') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="marital_status" class="block text-sm font-medium text-gray-700">Marital Status</label>
            <div class="mt-1">
                <select id="marital_status" name="marital_status" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Status</option>
                    <option value="Single" {{ old('marital_status', $application->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('marital_status', $application->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ old('marital_status', $application->marital_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="Widowed" {{ old('marital_status', $application->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
            </div>
            @error('marital_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
            <div class="mt-1">
                <select id="nationality" name="nationality" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" x-data="{}" @change="$dispatch('nationality-change', $el.value)">
                    <option value="">Select Nationality</option>
                    <option value="Tanzanian" {{ old('nationality', $application->nationality) == 'Tanzanian' ? 'selected' : '' }}>Tanzanian</option>
                    <option value="Foreign" {{ old('nationality', $application->nationality) == 'Foreign' ? 'selected' : '' }}>Foreign</option>
                </select>
            </div>
            @error('nationality') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div x-data="{ nationality: '{{ old('nationality', $application->nationality) }}' }" @nationality-change.window="nationality = $event.detail">
            <div x-show="nationality === 'Tanzanian'">
                <label for="nin" class="block text-sm font-medium text-gray-700">NIDA Number (NIN)</label>
                <div class="mt-1">
                    <input type="text" name="nin" id="nin" value="{{ old('nin', $application->nin) }}" placeholder="20XXXXXXXXXXXXXXXXX" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                @error('nin') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div x-show="nationality === 'Foreign'">
                <label for="passport_number" class="block text-sm font-medium text-gray-700">Passport Number</label>
                <div class="mt-1">
                    <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $application->passport_number) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                @error('passport_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Continue
        </button>
    </div>
</form>
@endsection
