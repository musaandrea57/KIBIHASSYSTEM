@extends('layouts.portal')

@section('content')
<div class="space-y-8">
    <!-- Student Identity Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                Student Identity & Status
            </h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Full Name</p>
                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Registration Number</p>
                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $student->registration_number }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Programme</p>
                    <p class="mt-1 text-base text-gray-900">{{ $student->program->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Fee Status</p>
                    <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        Cleared
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Center Tabs -->
    <div x-data="{ activeTab: 'current' }" class="space-y-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'current'"
                    :class="activeTab === 'current' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Current Results ({{ $currentYear->name ?? 'N/A' }})
                </button>
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Academic History
                </button>
                <button @click="activeTab = 'transcript'"
                    :class="activeTab === 'transcript' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Transcripts
                </button>
            </nav>
        </div>

        <!-- Current Results Tab -->
        <div x-show="activeTab === 'current'" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Semester I (Internal) -->
            <div class="bg-white shadow rounded-lg border border-gray-200 flex flex-col h-full col-span-1 lg:col-span-2">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Semester I Results</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Internal
                    </span>
                </div>
                <div class="flex-1 p-6">
                    @if($resultsSem1->count() > 0)
                        @php
                            $totalCredits = 0;
                            $totalPoints = 0;
                        @endphp
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Code</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Module Name</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">CW</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">SE</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Total</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Grade</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Credits</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">Points</th>
                                        <th scope="col" class="px-3 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($resultsSem1 as $result)
                                    @php
                                        $credits = $result->moduleOffering->module->credits ?? 10;
                                        $points = $result->points ?? 0;
                                        $totalCredits += $credits;
                                        $totalPoints += $points;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r">{{ $result->moduleOffering->module->code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 border-r">{{ $result->moduleOffering->module->name }}</td>
                                        <td class="px-3 py-3 text-sm text-center text-gray-500 border-r">{{ $result->cw_mark }}</td>
                                        <td class="px-3 py-3 text-sm text-center text-gray-500 border-r">{{ $result->se_mark }}</td>
                                        <td class="px-3 py-3 text-sm text-center font-bold text-gray-900 border-r">{{ $result->total_mark }}</td>
                                        <td class="px-3 py-3 text-sm text-center font-bold {{ $result->grade === 'F' ? 'text-red-600' : 'text-gray-900' }} border-r">
                                            {{ $result->grade ?? '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-center text-gray-900 border-r">{{ $credits }}</td>
                                        <td class="px-3 py-3 text-sm text-center text-gray-900 border-r">{{ number_format($points, 1) }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-500">{{ $result->remark }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold">
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-right text-gray-900 border-r">Semester Total</td>
                                        <td class="px-3 py-3 text-center text-gray-900 border-r">{{ $totalCredits }}</td>
                                        <td class="px-3 py-3 text-center text-gray-900 border-r">{{ number_format($totalPoints, 1) }}</td>
                                        <td class="px-3 py-3"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-4 py-3 text-right text-gray-900 border-r">Semester GPA</td>
                                        <td class="px-3 py-3 text-center text-blue-700 border-r text-lg">
                                            {{ $totalCredits > 0 ? number_format($totalPoints / $totalCredits, 1) : '0.0' }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-500">
                                            {{-- Classification logic could go here --}}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Grading System</h4>
                                <div class="bg-gray-50 rounded-md p-4 border border-gray-200 text-xs">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>A: 80-100 (Excellent) - 4.0</div>
                                        <div>B: 65-79 (Good) - 3.0</div>
                                        <div>C: 50-64 (Satisfactory) - 2.0</div>
                                        <div>D: 40-49 (Poor) - 1.0</div>
                                        <div>F: 0-39 (Failure) - 0.0</div>
                                        <div>I: Incomplete</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-end justify-end">
                                 <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Semester I Statement
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No Semester I results published yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Semester II (Official NACTVET) -->
            <div class="bg-white shadow rounded-lg border border-gray-200 flex flex-col h-full">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Semester II Results</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Official NACTVET
                    </span>
                </div>
                <div class="flex-1 p-6 flex flex-col justify-center items-center text-center">
                    @if(isset($officialResults) && $officialResults->count() > 0)
                        <div class="space-y-4 w-full">
                             @foreach($officialResults as $upload)
                             <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                 <h4 class="font-bold text-gray-900">{{ $upload->title ?? 'Official Results PDF' }}</h4>
                                 <p class="text-sm text-gray-500 mb-4">Published: {{ \Carbon\Carbon::parse($upload->created_at)->format('d M Y') }}</p>
                                 <a href="{{ Storage::url($upload->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 w-full justify-center">
                                    <svg class="mr-2 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0111.293 2.707L15.293 6.707A2 2 0 0116 8.586V17a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    Download Official PDF
                                 </a>
                             </div>
                             @endforeach
                        </div>
                    @else
                        <div class="py-6">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
                                <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Results Pending Verification</h3>
                            <p class="mt-2 text-gray-500 max-w-sm">
                                Official Semester II results are currently being processed by NACTVET. They will be available here once published.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div x-show="activeTab === 'history'" style="display: none;">
            @if(isset($historyResults) && $historyResults->count() > 0)
                <div class="space-y-8">
                    @foreach($historyResults as $groupName => $results)
                        <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $groupName }}</h3>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Code</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Module Name</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase">Grade</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold text-gray-700 uppercase">Points</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($results as $res)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $res->moduleOffering->module->code }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $res->moduleOffering->module->name }}</td>
                                                <td class="px-3 py-3 text-sm text-center font-bold">{{ $res->grade }}</td>
                                                <td class="px-3 py-3 text-sm text-center">{{ $res->points }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Academic History</h3>
                    <p class="mt-1 text-sm text-gray-500">Prior academic results will appear here.</p>
                </div>
            @endif
        </div>

        <!-- Transcript Tab -->
        <div x-show="activeTab === 'transcript'" style="display: none;">
            @if(isset($transcripts) && $transcripts->count() > 0)
                <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                    <ul class="divide-y divide-gray-200">
                        @foreach($transcripts as $transcript)
                        <li>
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex items-center">
                                    <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0111.293 2.707L15.293 6.707A2 2 0 0116 8.586V17a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600 truncate">
                                            Academic Transcript - Generated {{ \Carbon\Carbon::parse($transcript->created_at)->format('d M Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500">Version {{ $transcript->version }}</p>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="{{ Storage::url($transcript->file_path) }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-500">Download</a>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Transcripts Available</h3>
                    <p class="mt-1 text-sm text-gray-500">Your official transcript has not been generated yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
