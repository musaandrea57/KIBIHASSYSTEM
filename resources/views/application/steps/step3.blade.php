@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Contact & Residence Details</h2>
    <p class="mt-1 text-sm text-gray-600">Provide your current and permanent address information.</p>
</div>

<form method="POST" action="{{ route('application.step3.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="current_address" class="block text-sm font-medium text-gray-700">Current Residential Address</label>
            <div class="mt-1">
                <textarea id="current_address" name="current_address" rows="3" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('current_address', $application->current_address) }}</textarea>
            </div>
            @error('current_address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="permanent_address" class="block text-sm font-medium text-gray-700">Permanent Home Address</label>
            <div class="mt-1">
                <textarea id="permanent_address" name="permanent_address" rows="3" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('permanent_address', $application->permanent_address) }}</textarea>
            </div>
            <p class="mt-1 text-sm text-gray-500">If same as current address, please repeat it here.</p>
            @error('permanent_address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="region" class="block text-sm font-medium text-gray-700">Region / State</label>
            <div class="mt-1">
                <input type="text" name="region" id="region" required value="{{ old('region', $application->region) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('region') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
            <div class="mt-1">
                <input type="text" name="country" id="country" required value="{{ old('country', $application->country ?? 'Tanzania') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('country') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 2]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Continue
        </button>
    </div>
</form>
@endsection
