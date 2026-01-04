@extends('layouts.portal')

@section('title', 'Teacher Performance Overview')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Teacher Performance Overview</h1>
            <p class="text-sm text-gray-500">Executive metrics and staff effectiveness monitoring</p>
        </div>
        <div class="flex space-x-2">
             <a href="{{ route('principal.teachers.export', array_merge(['report' => 'overview'], request()->all())) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 shadow-sm">
                <i class="fas fa-download mr-1"></i> Export Report
             </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-2 items-center">
            <!-- Search -->
            <div class="flex-grow min-w-[200px]">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" placeholder="Search Teacher..." value="{{ $filters['search'] ?? '' }}" class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Academic Year -->
            <div class="w-40">
                <select name="academic_year_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach($options['academic_years'] as $year)
                        <option value="{{ $year->id }}" {{ (isset($filters['academic_year_id']) && $filters['academic_year_id'] == $year->id) ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div class="w-32">
                <select name="semester_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Semesters</option>
                    @foreach($options['semesters'] as $semester)
                        <option value="{{ $semester->id }}" {{ (isset($filters['semester_id']) && $filters['semester_id'] == $semester->id) ? 'selected' : '' }}>
                            {{ $semester->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Programme -->
            <div class="w-48">
                <select name="program_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Programmes</option>
                    @foreach($options['programs'] as $program)
                        <option value="{{ $program->id }}" {{ (isset($filters['program_id']) && $filters['program_id'] == $program->id) ? 'selected' : '' }}>
                            {{ $program->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Department -->
            <div class="w-40">
                <select name="department_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Depts</option>
                    @foreach($options['departments'] as $dept)
                        <option value="{{ $dept->id }}" {{ (isset($filters['department_id']) && $filters['department_id'] == $dept->id) ? 'selected' : '' }}>
                            {{ $dept->code ?? substr($dept->name, 0, 10) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex items-center space-x-2">
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Start Date">
                <span class="text-gray-500">-</span>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="End Date">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>

            <!-- Reset -->
            @if(collect($filters)->except(['page', 'academic_year_id'])->isNotEmpty())
                <a href="{{ url()->current() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- KPI Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
        <!-- Delivery Rate -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-medium">Avg Delivery Rate</p>
            <div class="flex items-end space-x-2 mt-1">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($kpis['avg_delivery_rate'], 1) }}%</h3>
                <!-- Trend -->
                @php $trend = $trends['delivery_rate']; @endphp
                <span class="text-xs {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }} mb-1">
                    <i class="fas fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }}"></i> {{ abs($trend) }}%
                </span>
            </div>
        </div>

        <!-- Attendance Completion -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-medium">Attd. Completion</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($kpis['attendance_completion'], 1) }}%</h3>
        </div>

        <!-- On-time Upload -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-medium">On-time Uploads</p>
            <div class="flex items-end space-x-2 mt-1">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($kpis['on_time_coursework'], 1) }}%</h3>
                @php $trend = $trends['upload_timeliness']; @endphp
                <span class="text-xs {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }} mb-1">
                    <i class="fas fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }}"></i> {{ abs($trend) }}%
                </span>
            </div>
        </div>

         <!-- Results Compliance -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <p class="text-xs text-gray-500 uppercase font-medium">Results Compliance</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($kpis['results_compliance'], 1) }}%</h3>
        </div>

         <!-- Avg Evaluation -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 col-span-1 xl:col-span-1">
            <p class="text-xs text-gray-500 uppercase font-medium">Avg Rating</p>
             <div class="flex items-end space-x-2 mt-1">
                <h3 class="text-2xl font-bold text-gray-900">{{ number_format($kpis['avg_evaluation'], 1) }}</h3>
                <span class="text-xs text-gray-400 mb-1">/ 5.0</span>
            </div>
             <p class="text-xs text-gray-400 mt-1">{{ $kpis['evaluation_count'] }} responses</p>
        </div>
        
         <!-- Needing Attention -->
        <div class="p-4 rounded-lg shadow-sm border bg-red-50 border-red-100 col-span-1 xl:col-span-2">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-red-600 uppercase font-semibold">Needs Attention</p>
                    <h3 class="text-3xl font-bold text-red-700 mt-1">{{ $kpis['needing_attention'] }}</h3>
                    <p class="text-xs text-red-500 mt-1">Teachers flagged for review</p>
                </div>
                <a href="{{ route('principal.teachers.alerts') }}" class="px-3 py-1 bg-white border border-red-200 text-red-600 text-xs rounded shadow-sm hover:bg-red-50">View Alerts</a>
            </div>
        </div>
    </div>

    <!-- Ranked Teacher Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Teacher Rankings</h2>
            <div class="text-sm text-gray-500">
                Sorted by Performance Index
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Rank</th>
                        <th class="px-6 py-3">Teacher</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3 text-center">Index (0-100)</th>
                        <th class="px-6 py-3 text-center">Delivery</th>
                        <th class="px-6 py-3 text-center">Attd.</th>
                        <th class="px-6 py-3 text-center">Rating</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($teachers as $index => $teacher)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-500">#{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600">
                                    {{ substr($teacher->name, 0, 2) }}
                                </div>
                                <div>
                                    <a href="{{ route('principal.teachers.show', $teacher) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $teacher->name }}</a>
                                    <div class="text-xs text-gray-500">ID: {{ $teacher->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $teacher->staffProfile->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $teacher->metrics['performance_index'] >= 80 ? 'bg-green-100 text-green-800' : 
                                  ($teacher->metrics['performance_index'] >= 60 ? 'bg-blue-100 text-blue-800' : 
                                  'bg-yellow-100 text-yellow-800') }}">
                                {{ $teacher->metrics['performance_index'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">
                            {{ $teacher->metrics['delivery_rate'] !== null ? $teacher->metrics['delivery_rate'].'%' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">
                            {{ $teacher->metrics['attendance_completion'] !== null ? $teacher->metrics['attendance_completion'].'%' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600">
                            {{ $teacher->metrics['evaluation_rating'] }}
                        </td>
                        <td class="px-6 py-4 text-center">
                             @php
                                $status = $teacher->performance_status;
                                $color = match($status) {
                                    'Excellent' => 'text-green-600 bg-green-50 border border-green-100',
                                    'Good' => 'text-blue-600 bg-blue-50 border border-blue-100',
                                    'Needs Attention' => 'text-red-600 bg-red-50 border border-red-100',
                                    default => 'text-gray-600 bg-gray-50 border border-gray-100'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $color }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('principal.teachers.show', $teacher) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">View Scorecard</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-search text-gray-300 text-3xl mb-3"></i>
                                <p>No teacher performance data available for current filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
