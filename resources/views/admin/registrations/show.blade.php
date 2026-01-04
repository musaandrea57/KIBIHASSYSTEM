@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Registration') }}
        </h2></div>

    <div class="mb-6">
                <a href="{{ route('admin.registrations.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </a>
            </div>

            <!-- Student Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Student Details</h3>
                            <p class="mt-1 text-sm text-gray-600">Name: <span class="font-semibold">{{ $registration->student->first_name }} {{ $registration->student->last_name }}</span></p>
                            <p class="text-sm text-gray-600">Reg #: <span class="font-semibold">{{ $registration->student->registration_number }}</span></p>
                            <p class="text-sm text-gray-600">Program: <span class="font-semibold">{{ $registration->program->name }}</span></p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Session Details</h3>
                            <p class="mt-1 text-sm text-gray-600">Academic Year: <span class="font-semibold">{{ $registration->academicYear->name }}</span></p>
                            <p class="text-sm text-gray-600">Semester: <span class="font-semibold">{{ $registration->semester->name }}</span></p>
                            <p class="text-sm text-gray-600">Level: <span class="font-semibold">NTA {{ $registration->nta_level }}</span></p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Status</h3>
                             <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                {{ $registration->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($registration->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                   ($registration->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($registration->status) }}
                            </span>
                            @if($registration->submitted_at)
                                <p class="mt-1 text-xs text-gray-500">Submitted: {{ $registration->submitted_at->format('M d, Y H:i') }}</p>
                            @endif
                </div>
            </div>

            <!-- Modules -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Selected Modules</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $totalCredits = 0; @endphp
                                @foreach($registration->items as $item)
                                    @php $totalCredits += $item->credits_snapshot; @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->moduleOffering->module->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->moduleOffering->module->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->credits_snapshot }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-bold">
                                    <td class="px-6 py-4" colspan="2">Total Credits</td>
                                    <td class="px-6 py-4">{{ $totalCredits }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($registration->status === 'submitted')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                        <div class="flex gap-4">
                            <form method="POST" action="{{ route('admin.registrations.approve', $registration) }}">
                                @csrf
                                <x-primary-button class="bg-green-600 hover:bg-green-700 focus:ring-green-500">
                                    Approve Registration
                                </x-primary-button>
                            </form>
                            
                            <button type="button" onclick="document.getElementById('reject-form').classList.toggle('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Reject
                            </button>
                        </div>

                        <div id="reject-form" class="hidden mt-4">
                            <form method="POST" action="{{ route('admin.registrations.reject', $registration) }}">
                                @csrf
                                <div>
                                    <x-input-label for="reason" :value="__('Rejection Reason')" />
                                    <textarea id="reason" name="reason" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                                </div>
                                <div class="mt-2">
                                    <x-danger-button>Confirm Rejection</x-danger-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection