@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registration Details') }}
        </h2></div>

    <div class="mb-6">
                <a href="{{ route('student.registration.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Overview
                </a>
            </div>

            <!-- Status Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $registration->academicYear->name }} - {{ $registration->semester->name }}
                            </h3>
                            <p class="text-sm text-gray-500">Submitted: {{ $registration->submitted_at ? $registration->submitted_at->format('M d, Y H:i') : 'Not Submitted' }}</p>
                            @if($registration->approved_at)
                                <p class="text-sm text-gray-500">Approved: {{ $registration->approved_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                             <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                {{ $registration->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($registration->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                   ($registration->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($registration->status) }}
                            </span>

                    @if($registration->status === 'rejected' && $registration->rejection_reason)
                        <div class="mt-4 p-4 bg-red-50 rounded-md">
                            <h4 class="text-sm font-medium text-red-800">Rejection Reason:</h4>
                            <p class="mt-1 text-sm text-red-700">{{ $registration->rejection_reason }}</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('student.registration.create') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit and Resubmit</a>
                        </div>
                    @endif
                    
                    @if($registration->status === 'draft')
                         <div class="mt-4">
                            <a href="{{ route('student.registration.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Continue Editing
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modules List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registered Modules</h3>
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

        </div>
    </div>

@endsection