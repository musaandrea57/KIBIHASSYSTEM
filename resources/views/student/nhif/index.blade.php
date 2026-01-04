@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('NHIF Membership') }}</h2>
        <p class="text-gray-600">Manage your health insurance details.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            @if($membership)
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Membership Details</h3>
                                    <p class="text-sm text-gray-500">Provided by NHIF</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-bold bg-{{ $membership->status_badge }}-100 text-{{ $membership->status_badge }}-800">
                                    {{ strtoupper(str_replace('_', ' ', $membership->status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">NHIF Number</label>
                                    <div class="text-xl font-mono text-gray-900">{{ $membership->nhif_number }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Scheme</label>
                                    <div class="text-gray-900">{{ $membership->scheme_name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Expiry Date</label>
                                    <div class="text-gray-900 {{ $membership->expiry_date && $membership->expiry_date < now()->addDays(30) ? 'text-red-600 font-bold' : '' }}">
                                        {{ $membership->expiry_date ? $membership->expiry_date->format('d M Y') : 'N/A' }}
                                        @if($membership->expiry_date && $membership->expiry_date < now()->addDays(30))
                                            <span class="text-xs ml-2 text-red-600">(Expiring Soon)</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Last Verified</label>
                                    <div class="text-gray-900">{{ $membership->verified_at ? $membership->verified_at->format('d M Y H:i') : 'Pending' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($membership->status == 'active' && $membership->expiry_date < now()->addDays(30))
                            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <!-- Warning Icon -->
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Your membership is expiring soon. Please visit the welfare office or NHIF to renew.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <p class="text-blue-700">You do not have a linked NHIF membership record.</p>
                </div>

                <div class="max-w-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Link NHIF Card</h3>
                    <form action="{{ route('student.nhif.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="nhif_number" class="block text-sm font-medium text-gray-700">NHIF Card Number (12 Digits)</label>
                            <input type="text" name="nhif_number" id="nhif_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50" placeholder="123456789012" required>
                        </div>
                        <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white font-bold py-2 px-4 rounded">
                            Submit for Verification
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endsection
