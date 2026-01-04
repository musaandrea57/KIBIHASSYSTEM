@extends('layouts.portal')

@section('title', 'Teacher Scorecard: ' . $teacher->name)

@section('content')
<div class="space-y-6">
    <!-- Identity Header & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-xl font-bold text-gray-500 overflow-hidden">
                @if($teacher->profile_photo_path)
                    <img src="{{ Storage::url($teacher->profile_photo_path) }}" alt="{{ $teacher->name }}" class="h-full w-full object-cover">
                @else
                    {{ substr($teacher->name, 0, 2) }}
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h1>
                <div class="flex items-center text-sm text-gray-500 space-x-4">
                    <span><i class="fas fa-id-badge mr-1"></i> {{ $teacher->id }}</span>
                    <span><i class="fas fa-building mr-1"></i> {{ $teacher->staffProfile->department->name ?? 'Unassigned' }}</span>
                    <span><i class="fas fa-book mr-1"></i> {{ $assignments->count() }} Assigned Modules</span>
                </div>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('principal.teachers.export', array_merge(['teacher' => $teacher->id, 'report' => 'teacher', 'type' => 'pdf'], request()->all())) }}" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-1"></i> PDF Report
            </a>
             <a href="{{ route('principal.teachers.export', array_merge(['teacher' => $teacher->id, 'report' => 'teacher', 'type' => 'excel'], request()->all())) }}" class="px-3 py-2 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-excel mr-1"></i> Data
            </a>
        </div>
    </div>

    <!-- Data Quality Warnings -->
    @php
        $missingData = [];
        if (is_null($metrics['delivery_rate'])) $missingData[] = "Class Delivery Rate (No sessions recorded)";
        if (is_null($metrics['attendance_completion'])) $missingData[] = "Attendance Completion (No attendance logs)";
    @endphp
    @if(count($missingData) > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <span class="font-medium">Data Quality Warning:</span> 
                    Some metrics are incomplete due to missing underlying data:
                    <ul class="list-disc list-inside ml-2 mt-1">
                        @foreach($missingData as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Score Summary & Performance Index -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Index Card -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-1">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Performance Index</h3>
            <div class="flex items-center justify-center mb-4">
                 <div class="relative w-32 h-32">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <path class="text-gray-200" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.8" />
                        <path class="{{ $metrics['performance_index'] >= 80 ? 'text-green-500' : ($metrics['performance_index'] >= 60 ? 'text-blue-500' : 'text-yellow-500') }}" 
                              stroke-dasharray="{{ $metrics['performance_index'] }}, 100" 
                              d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                              fill="none" stroke="currentColor" stroke-width="3.8" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $metrics['performance_index'] }}</span>
                        <span class="text-xs text-gray-500">/ 100</span>
                    </div>
                </div>
            </div>
            <div class="text-center">
                 @php
                    $status = $metrics['performance_index'] >= 80 ? 'Excellent' : ($metrics['performance_index'] >= 60 ? 'Good' : 'Needs Attention');
                    $color = $metrics['performance_index'] >= 80 ? 'text-green-600 bg-green-50' : ($metrics['performance_index'] >= 60 ? 'text-blue-600 bg-blue-50' : 'text-yellow-600 bg-yellow-50');
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $color }}">{{ $status }}</span>
            </div>
             <!-- Peer Comparison -->
             @if($peers)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Department Avg</span>
                    <span class="font-medium text-gray-700">{{ $peers['department_avg_index'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-gray-500 h-1.5 rounded-full" style="width: {{ $peers['department_avg_index'] }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2 mb-1">
                    <span>Institution Avg</span>
                    <span class="font-medium text-gray-700">{{ $peers['institution_avg_index'] }}</span>
                </div>
                 <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-gray-400 h-1.5 rounded-full" style="width: {{ $peers['institution_avg_index'] }}%"></div>
                </div>
            </div>
            @endif
        </div>

        <!-- Detailed Metrics -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
            <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Key Performance Indicators</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                 <!-- Delivery Rate -->
                <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-sm text-gray-600">Delivery Rate</span>
                        <span class="text-lg font-bold text-gray-900">{{ $metrics['delivery_rate'] ?? 'N/A' }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $metrics['delivery_rate'] ?? 0 }}%"></div>
                    </div>
                     <p class="text-xs text-gray-400 mt-1">Weight: {{ config('performance.weights.delivery_rate') }}%</p>
                </div>

                 <!-- Attendance Completion -->
                 <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-sm text-gray-600">Attendance Comp.</span>
                        <span class="text-lg font-bold text-gray-900">{{ $metrics['attendance_completion'] ?? 'N/A' }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $metrics['attendance_completion'] ?? 0 }}%"></div>
                    </div>
                     <p class="text-xs text-gray-400 mt-1">Weight: {{ config('performance.weights.attendance_completion') }}%</p>
                </div>

                 <!-- Upload Timeliness -->
                 <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-sm text-gray-600">Timeliness</span>
                        <span class="text-lg font-bold text-gray-900">{{ $metrics['upload_timeliness'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $metrics['upload_timeliness'] }}%"></div>
                    </div>
                     <p class="text-xs text-gray-400 mt-1">Weight: {{ config('performance.weights.assessment_timeliness') }}%</p>
                </div>

                 <!-- Evaluation Rating -->
                 <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-sm text-gray-600">Student Rating</span>
                        <span class="text-lg font-bold text-gray-900">{{ $metrics['evaluation_rating'] }} <span class="text-xs font-normal text-gray-500">/ 5.0</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($metrics['evaluation_rating']/5)*100 }}%"></div>
                    </div>
                     <p class="text-xs text-gray-400 mt-1">Weight: {{ config('performance.weights.evaluation_rating') }}%</p>
                </div>

                 <!-- Results Compliance -->
                 <div>
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-sm text-gray-600">Results Compliance</span>
                        <span class="text-lg font-bold text-gray-900">{{ $metrics['results_compliance'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $metrics['results_compliance'] }}%"></div>
                    </div>
                     <p class="text-xs text-gray-400 mt-1">Weight: {{ config('performance.weights.results_compliance') }}%</p>
                </div>
            </div>

            <!-- Alerts Section -->
            @if(count($alerts) > 0)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Active Alerts</h4>
                <div class="space-y-2">
                    @foreach($alerts as $alert)
                    <div class="flex items-start p-3 rounded-md {{ $alert['type'] == 'critical' ? 'bg-red-50 text-red-700' : ($alert['type'] == 'warning' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-700') }}">
                        <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                        <div class="text-sm">
                            <span class="font-semibold">{{ $alert['message'] }}:</span>
                            Value is {{ $alert['value'] }} (Threshold: {{ $alert['threshold'] }})
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Evidence Tabs -->
    <div x-data="{ activeTab: 'modules' }" class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 px-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'modules'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'modules', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'modules' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Assigned Modules
                </button>
                <button @click="activeTab = 'evaluations'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'evaluations', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'evaluations' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Student Evaluations
                </button>
                 <button @click="activeTab = 'results'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'results', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'results' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Academic Results
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Modules Tab -->
            <div x-show="activeTab === 'modules'">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Assigned Modules</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level/Prog</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('principal.teachers.module', $assignment->moduleOffering->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $assignment->moduleOffering->module->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $assignment->moduleOffering->module->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    NTA {{ $assignment->moduleOffering->module->program->nta_level ?? 'N/A' }}
                                    <div class="text-xs text-gray-400">{{ $assignment->moduleOffering->module->program->code ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $assignment->academicYear->name ?? 'N/A' }} - {{ $assignment->semester->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('principal.teachers.module', $assignment->moduleOffering->id) }}" class="text-indigo-600 hover:text-indigo-900">View Performance</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No active assignments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Evaluations Tab -->
            <div x-show="activeTab === 'evaluations'" style="display: none;">
                 <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Student Evaluations</h4>
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($evidence['evaluations'] as $evaluation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $evaluation->moduleOffering->module->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $evaluation->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($evaluation->answers->avg('rating'), 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $confidenceColor = match($evaluation->confidence) {
                                            'High' => 'bg-green-100 text-green-800',
                                            'Medium' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-red-100 text-red-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $confidenceColor }}">
                                        {{ $evaluation->confidence }} ({{ $evaluation->response_count }})
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No evaluations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

             <!-- Results Tab -->
            <div x-show="activeTab === 'results'" style="display: none;">
                 <h4 class="text-lg font-medium text-gray-900 mb-4">Academic Results Performance</h4>
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Mark</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($evidence['results'] as $result)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $result->moduleOffering->module->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $result->total_students }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($result->avg_mark, 1) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No results data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
