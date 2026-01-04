@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Health, Emergency & Welfare</h2>
    <p class="mt-1 text-sm text-gray-600">Please provide health information and emergency contact details.</p>
</div>

<form method="POST" action="{{ route('application.step6.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div>
            <label for="nhif_card_number" class="block text-sm font-medium text-gray-700">NHIF Card Number (Optional)</label>
            <div class="mt-1">
                <input type="text" name="nhif_card_number" id="nhif_card_number" value="{{ old('nhif_card_number', $application->nhif_card_number) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('nhif_card_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="medical_conditions" class="block text-sm font-medium text-gray-700">Medical Conditions / Allergies</label>
            <div class="mt-1">
                <textarea id="medical_conditions" name="medical_conditions" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('medical_conditions', $application->medical_conditions) }}</textarea>
            </div>
            <p class="mt-1 text-sm text-gray-500">List any medical conditions we should be aware of (e.g. Asthma, Diabetes).</p>
            @error('medical_conditions') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2" x-data="{ hasDisability: {{ old('has_disability', $application->has_disability) ? 'true' : 'false' }} }">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="has_disability" name="has_disability" type="checkbox" x-model="hasDisability" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="has_disability" class="font-medium text-gray-700">Do you have any physical disability?</label>
                    <p class="text-gray-500">This information helps us prepare necessary accommodations.</p>
                </div>
            </div>

            <div x-show="hasDisability" class="mt-4">
                <label for="disability_details" class="block text-sm font-medium text-gray-700">Please describe your disability</label>
                <div class="mt-1">
                    <textarea id="disability_details" name="disability_details" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('disability_details', $application->disability_details) }}</textarea>
                </div>
                @error('disability_details') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="sm:col-span-2 border-t border-gray-200 pt-6 mt-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                <div>
                    <label for="emergency_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <div class="mt-1">
                        <input type="text" name="emergency_contact[name]" id="emergency_name" required value="{{ old('emergency_contact.name', $application->emergency_contact['name'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('emergency_contact.name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="emergency_relationship" class="block text-sm font-medium text-gray-700">Relationship</label>
                    <div class="mt-1">
                        <input type="text" name="emergency_contact[relationship]" id="emergency_relationship" required value="{{ old('emergency_contact.relationship', $application->emergency_contact['relationship'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('emergency_contact.relationship') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="emergency_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <div class="mt-1">
                        <input type="tel" name="emergency_contact[phone]" id="emergency_phone" required value="{{ old('emergency_contact.phone', $application->emergency_contact['phone'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('emergency_contact.phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="emergency_address" class="block text-sm font-medium text-gray-700">Address</label>
                    <div class="mt-1">
                        <input type="text" name="emergency_contact[address]" id="emergency_address" required value="{{ old('emergency_contact.address', $application->emergency_contact['address'] ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    @error('emergency_contact.address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between">
        <a href="{{ route('application.step', ['step' => 5]) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Save & Continue
        </button>
    </div>
</form>
@endsection
