@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Academic Reports Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Executive overview of academic performance, registration, and compliance.</p>
        </div>

        <!-- Filter Bar -->
        @include('principal.reports.partials.filter-bar')

        <!-- Alerts Section -->
        @if(count($alerts) > 0)
        <div class="mb-8 bg-white rounded-lg shadow-sm border border-orange-200 overflow-hidden">
            <div class="bg-orange-50 px-4 py-3 border-b border-orange-200 flex items-center">
                <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                <h3 class="text-sm font-bold text-orange-800 uppercase tracking-wide">Attention Required</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($alerts as $alert)
                <div class="p-4 flex items-start hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0">
                        @if($alert['type'] == 'warning')
                            <i class="fas fa-clock text-yellow-500 text-xl mt-1"></i>
                        @elseif($alert['type'] == 'danger')
                            <i class="fas fa-times-circle text-red-500 text-xl mt-1"></i>
                        @else
                            <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <h4 class="text-sm font-semibold text-gray-900">{{ $alert['title'] }}</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $alert['message'] }}</p>
                    </div>
                    <div class="ml-4">
                        <a href="{{ $alert['link'] }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                            View Details &rarr;
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Enrolled -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Enrolled</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($kpis['total_enrolled']) }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Active students</span>
                </div>
            </div>

            <!-- Semester Registration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Semester Registered</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($kpis['semester_registered']) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-full">
                        <i class="fas fa-file-signature text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="{{ $kpis['registration_rate'] >= 80 ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                        {{ $kpis['registration_rate'] }}%
                    </span>
                    <span class="text-gray-500 ml-2">of total enrolled</span>
                </div>
            </div>

            <!-- Results Published -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Results Published</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($kpis['results_published']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">{{ $kpis['results_published_rate'] }}% of uploaded results</span>
                </div>
            </div>

            <!-- Pass Rate -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Overall Pass Rate</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $kpis['pass_rate'] }}%</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-full">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Avg GPA: {{ $kpis['avg_gpa'] }}</span>
                </div>
            </div>
        </div>

        <!-- Navigation Grid -->
        <h2 class="text-lg font-bold text-gray-900 mb-4">Report Categories</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Performance Reports -->
            <a href="{{ route('principal.reports.performance') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-indigo-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-600 transition-colors">
                            <i class="fas fa-chart-bar text-indigo-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Performance Reports</h3>
                    </div>
                    <p class="text-sm text-gray-500">Programme pass rates, module grade distributions, and student performance listings.</p>
                </div>
            </a>

            <!-- Registration Reports -->
            <a href="{{ route('principal.reports.registration') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-blue-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-600 transition-colors">
                            <i class="fas fa-id-card text-blue-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Registration Reports</h3>
                    </div>
                    <p class="text-sm text-gray-500">Enrollment summaries, module registrations, and registration compliance.</p>
                </div>
            </a>

            <!-- Progression & Retention -->
            <a href="{{ route('principal.reports.progression') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-green-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-600 transition-colors">
                            <i class="fas fa-user-graduate text-green-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Progression & Retention</h3>
                    </div>
                    <p class="text-sm text-gray-500">Student promotion status, repetition rates, and attrition analysis.</p>
                </div>
            </a>

            <!-- Assessments Workflow -->
            <a href="{{ route('principal.reports.workflow') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-orange-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-orange-100 rounded-lg group-hover:bg-orange-600 transition-colors">
                            <i class="fas fa-tasks text-orange-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Assessments Workflow</h3>
                    </div>
                    <p class="text-sm text-gray-500">Track results upload status, approval delays, and publishing timeline.</p>
                </div>
            </a>

            <!-- Compliance -->
            <a href="{{ route('principal.reports.compliance') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-red-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-red-100 rounded-lg group-hover:bg-red-600 transition-colors">
                            <i class="fas fa-shield-alt text-red-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Compliance Reports</h3>
                    </div>
                    <p class="text-sm text-gray-500">NACTVET registration coverage, data completeness, and regulatory adherence.</p>
                </div>
            </a>

            <!-- Archive / Exports -->
            <a href="{{ route('principal.reports.export') }}" class="block group">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:border-gray-300 transition-all h-full">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-gray-100 rounded-lg group-hover:bg-gray-600 transition-colors">
                            <i class="fas fa-archive text-gray-600 group-hover:text-white text-xl"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Exports & Archive</h3>
                    </div>
                    <p class="text-sm text-gray-500">Access historical reports and bulk data exports.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
