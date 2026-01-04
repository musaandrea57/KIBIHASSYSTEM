@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Details') }}
        </h2></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Student Profile Card -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        <div class="w-32 h-32 mx-auto mb-4">
                            @if($student->profile_photo_path)
                                <img src="{{ Storage::url($student->profile_photo_path) }}" alt="{{ $student->user->name }}" class="w-full h-full rounded-full object-cover border-4 border-gray-200">
                            @else
                                <div class="w-full h-full rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-3xl font-bold border-4 border-gray-200">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h3>
                        <p class="text-gray-500">{{ $student->registration_number }}</p>
                        
                        <div class="mt-6 border-t border-gray-100 pt-4 space-y-3 text-left">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Program</span>
                                <span class="block font-medium text-gray-900">{{ $student->program->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-wide">NTA Level</span>
                                <span class="block font-medium text-gray-900">{{ $student->current_nta_level }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Current Session</span>
                                <span class="block font-medium text-gray-900">{{ $student->currentAcademicYear->name ?? '-' }} ({{ $student->currentSemester->name ?? '-' }})</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-wide">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                    </div>
                </div>

                <!-- Academic Results & Registrations -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- Current Semester Registration -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Current Semester Modules</h3>
                        </div>
                        <div class="p-6">
                            @if($student->registrations->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Module Name</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($student->registrations as $reg)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $reg->module->code }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $reg->module->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $reg->module->credits }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="text-green-600 font-semibold">Registered</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 italic">No modules registered for this semester.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Results -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Academic Results</h3>
                        </div>
                        <div class="p-6">
                            @if(isset($results) && $results->count() > 0)
                                @foreach($results as $session => $sessionResults)
                                    <div class="mb-6 last:mb-0">
                                        <h4 class="font-bold text-gray-700 mb-2 border-b border-gray-200 pb-1">{{ $session }}</h4>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($sessionResults as $res)
                                                    <tr>
                                                        <td class="px-2 py-2 text-sm text-gray-900">
                                                            <div class="font-medium">{{ $res->module->code }}</div>
                                                            <div class="text-xs text-gray-500">{{ Str::limit($res->module->name, 30) }}</div>
                                                        </td>
                                                        <td class="px-2 py-2 text-center text-sm font-bold {{ $res->remarks == 'Pass' ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $res->grade }}
                                                        </td>
                                                        <td class="px-2 py-2 text-center text-sm">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $res->remarks == 'Pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                {{ $res->remarks }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500 italic">No academic results available yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Status (Placeholder) -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Financial Status</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div>
                                    <p class="text-sm text-green-700 font-bold">Tuition Fees</p>
                                    <p class="text-xs text-green-600">Semester 1 2025/2026</p>
                                </div>
                                <div class="text-right">
                                    <span class="block text-lg font-bold text-green-800">Paid</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
