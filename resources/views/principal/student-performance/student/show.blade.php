@extends('layouts.portal')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Profile</h1>
            <p class="mt-2 text-sm text-gray-600">Individual performance record and result statement.</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
             <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            <a href="{{ route('principal.student-performance.export', ['report' => 'student', 'student_id' => $student->id, 'format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                <i class="fas fa-file-pdf mr-2"></i> Download Statement
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Student Identity Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="p-6 text-center border-b border-gray-100">
                    <div class="mx-auto h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                        <span class="text-3xl font-bold text-indigo-600">{{ substr($student->user->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $student->registration_number }}</p>
                </div>
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Academic Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">NACTVET Reg No:</dt>
                            <dd class="font-medium text-gray-900">{{ $student->nactvet_registration_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Programme:</dt>
                            <dd class="font-medium text-gray-900 text-right pl-4">{{ $student->program->code }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Current NTA Level:</dt>
                            <dd class="font-medium text-gray-900">Level {{ $student->current_nta_level }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Intake Year:</dt>
                            <dd class="font-medium text-gray-900">{{ $student->created_at->format('Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Status:</dt>
                            <dd class="font-medium {{ $student->status == 'active' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($student->status) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statement of Results -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100 min-h-[500px]">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Official Statement of Results</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Published Results Only
                    </span>
                </div>

                @forelse($results as $period => $periodResults)
                    <div class="border-b border-gray-100 last:border-0">
                        <div class="bg-gray-50/50 px-6 py-3">
                            <h3 class="text-sm font-bold text-gray-900">{{ $period }}</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                        <th scope="col" class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                        <th scope="col" class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th scope="col" class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Point</th>
                                        <th scope="col" class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $periodPoints = 0;
                                        $periodCredits = 0;
                                    @endphp
                                    @foreach($periodResults as $result)
                                        @php
                                            if($result->grade_point !== null && $result->credits_snapshot > 0) {
                                                $periodPoints += $result->grade_point * $result->credits_snapshot;
                                                $periodCredits += $result->credits_snapshot;
                                            }
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $result->moduleOffering->module->code }}</div>
                                                <div class="text-xs text-gray-500">{{ Str::limit($result->moduleOffering->module->name, 40) }}</div>
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                                {{ $result->credits_snapshot }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                                {{ $result->grade ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                                                {{ $result->grade_point ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $result->total_mark >= 40 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $result->total_mark >= 40 ? 'PASS' : 'FAIL' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Period Summary -->
                                    <tr class="bg-gray-50 font-semibold">
                                        <td class="px-6 py-3 text-right text-sm text-gray-900">Semester GPA:</td>
                                        <td class="px-6 py-3 text-center text-sm text-gray-900" colspan="4">
                                            @if($periodCredits > 0)
                                                {{ number_format($periodPoints / $periodCredits, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No Published Results</h3>
                        <p class="mt-2 text-sm text-gray-500">This student has no published results visible at this time.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
