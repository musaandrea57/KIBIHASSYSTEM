@extends('layouts.portal')

@section('title', 'Module Performance: ' . $moduleOffering->module->code)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('principal.teachers.index') }}" class="hover:text-indigo-600">Teachers</a>
                    <span>/</span>
                    @if($moduleOffering->teacher)
                        <a href="{{ route('principal.teachers.show', $moduleOffering->teacher) }}" class="hover:text-indigo-600">{{ $moduleOffering->teacher->name }}</a>
                    @else
                        <span class="text-gray-400">Unassigned</span>
                    @endif
                    <span>/</span>
                    <span>Module Performance</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $moduleOffering->module->name }} ({{ $moduleOffering->module->code }})</h1>
                <p class="text-sm text-gray-500">{{ $moduleOffering->academicYear->name }} - {{ $moduleOffering->semester->name }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    NTA Level {{ $moduleOffering->module->program->nta_level ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Data Quality Warnings -->
    @php
        $warnings = [];
        if ($moduleOffering->moduleResults->isEmpty()) $warnings[] = "No results uploaded yet.";
        if ($stats['evaluation_score'] == 0) $warnings[] = "No student evaluations recorded.";
    @endphp
    @if(count($warnings) > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <span class="font-medium">Data Quality Warning:</span> 
                    <ul class="list-disc list-inside ml-2 mt-1">
                        @foreach($warnings as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pass Rate -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase">Pass Rate</h3>
            <div class="mt-2 flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['pass_rate'], 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['pass_rate'] }}%"></div>
            </div>
        </div>

        <!-- Average Mark -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase">Average Mark</h3>
            <div class="mt-2 flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['avg_mark'], 1) }}</span>
                <span class="ml-2 text-sm text-gray-500">/ 100</span>
            </div>
             <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['avg_mark'] }}%"></div>
            </div>
        </div>

        <!-- Student Feedback -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase">Student Feedback</h3>
            <div class="mt-2 flex items-baseline">
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['evaluation_score'], 1) }}</span>
                <span class="ml-2 text-sm text-gray-500">/ 5.0</span>
            </div>
            <div class="flex items-center mt-4">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($stats['evaluation_score']) ? 'text-yellow-400' : 'text-gray-300' }} mr-1"></i>
                @endfor
            </div>
        </div>
    </div>

    <!-- Student Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Student Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CA</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($moduleOffering->moduleResults as $result)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->student->id ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->student->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $result->ca_score }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $result->exam_score }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">{{ $result->total_mark }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $result->grade }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($result->total_mark >= 40)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Pass</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Fail</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No results found for this module offering.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
