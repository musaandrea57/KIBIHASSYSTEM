@extends('layouts.portal')

@section('title', 'Performance Alerts & Exceptions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Exceptions & Alerts Queue</h1>
            <p class="text-sm text-gray-500">Teachers requiring immediate attention based on performance thresholds</p>
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

    <!-- Alerts Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-bell text-yellow-500 mr-2"></i> Active Alerts
            </h3>
        </div>
        
        @if(count($alerts) > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metric vs Threshold</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($alerts as $alert)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($alert['type'] == 'critical')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Critical
                            </span>
                        @elseif($alert['type'] == 'warning')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Warning
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Info
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                {{ substr($alert['teacher']->name, 0, 2) }}
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('principal.teachers.show', $alert['teacher']->id) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $alert['teacher']->name }}</a>
                                <div class="text-xs text-gray-500">{{ $alert['teacher']->staff_id ?? 'No Staff ID' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $alert['teacher']->staffProfile->department->code ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $alert['message'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="font-medium text-gray-900">{{ $alert['value'] }}</span>
                        <span class="text-gray-400 text-xs ml-1">(Target: {{ $alert['threshold'] }})</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('principal.teachers.show', $alert['teacher']->id) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-12 text-center">
            <div class="mx-auto h-12 w-12 text-green-400">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Alerts Found</h3>
            <p class="mt-1 text-sm text-gray-500">All teachers are performing within the expected thresholds for the selected period.</p>
        </div>
        @endif
    </div>
</div>
@endsection
