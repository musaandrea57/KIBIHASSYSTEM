@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">SMS Settings</h2>
                </div>

                <form method="POST" action="{{ route('admin.communication.sms.settings.update') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">SMS Provider</label>
                        <select name="settings[provider]" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="simulated" {{ ($settings->where('key', 'provider')->first()->value ?? '') == 'simulated' ? 'selected' : '' }}>Simulated (Test)</option>
                            <option value="nextsms" {{ ($settings->where('key', 'provider')->first()->value ?? '') == 'nextsms' ? 'selected' : '' }} disabled>NextSMS (Coming Soon)</option>
                            <option value="twilio" {{ ($settings->where('key', 'provider')->first()->value ?? '') == 'twilio' ? 'selected' : '' }} disabled>Twilio (Coming Soon)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Currently only Simulated provider is supported for development.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Sender ID</label>
                        <input type="text" name="settings[sender_id]" value="{{ $settings->where('key', 'sender_id')->first()->value ?? 'KIBIHAS' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="hidden" name="settings[is_enabled]" value="0">
                            <input type="checkbox" name="settings[is_enabled]" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ ($settings->where('key', 'is_enabled')->first()->value ?? '1') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Enable SMS Sending</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">If unchecked, no SMS will be sent by the system.</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
