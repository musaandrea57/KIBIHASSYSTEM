@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Student Performance Analytics</h1>
            <p class="mt-2 text-sm text-gray-600">Comprehensive overview of student academic performance and trends.</p>
        </div>

        <!-- Filter Bar -->
        @include('principal.student-performance.partials.filter-bar')

        <!-- Navigation -->
        @include('principal.student-performance.partials.nav')

        <!-- Data Quality Alerts -->
        @include('principal.student-performance.partials.data-quality-alert')

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <!-- Total Students -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Students</p>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($kpis['total_students']) }}</p>
            </div>

            <!-- Avg GPA -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative group">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg GPA</p>
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <i class="fas fa-graduation-cap text-purple-600 text-lg"></i>
                    </div>
                </div>
                @if($kpis['published_coverage'] > 0 && $kpis['avg_gpa'] !== null)
                    <p class="text-3xl font-bold text-gray-900">{{ $kpis['avg_gpa'] }}</p>
                    @if($kpis['published_coverage'] < 100)
                        <div class="absolute top-2 right-2">
                            <i class="fas fa-exclamation-circle text-amber-400" title="Partial data: Only {{ $kpis['published_coverage'] }}% of results published"></i>
                        </div>
                    @endif
                @else
                    <p class="text-3xl font-bold text-gray-300">N/A</p>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-48 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10 text-center">
                        Metrics unavailable until results are published.
                    </div>
                @endif
            </div>

            <!-- Pass Rate -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative group">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pass Rate</p>
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                </div>
                @if($kpis['published_coverage'] > 0 && $kpis['pass_rate'] !== null)
                    <p class="text-3xl font-bold text-green-600">{{ $kpis['pass_rate'] }}%</p>
                @else
                    <p class="text-3xl font-bold text-gray-300">N/A</p>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-48 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10 text-center">
                        Metrics unavailable until results are published.
                    </div>
                @endif
            </div>

            <!-- Fail Rate -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative group">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fail Rate</p>
                    <div class="p-2 bg-red-50 rounded-lg">
                        <i class="fas fa-times-circle text-red-600 text-lg"></i>
                    </div>
                </div>
                @if($kpis['published_coverage'] > 0 && $kpis['fail_rate'] !== null)
                    <p class="text-3xl font-bold text-red-600">{{ $kpis['fail_rate'] }}%</p>
                @else
                    <p class="text-3xl font-bold text-gray-300">N/A</p>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-48 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10 text-center">
                        Metrics unavailable until results are published.
                    </div>
                @endif
            </div>

            <!-- Carries -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative group">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Carry Count</p>
                    <div class="p-2 bg-orange-50 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-lg"></i>
                    </div>
                </div>
                @if($kpis['published_coverage'] > 0 && $kpis['carry_count'] !== null)
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($kpis['carry_count']) }}</p>
                @else
                    <p class="text-3xl font-bold text-gray-300">N/A</p>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-48 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10 text-center">
                        Metrics unavailable until results are published.
                    </div>
                @endif
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- GPA Distribution -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">GPA Distribution</h3>
                <div class="h-64" id="gpaDistributionChart"></div>
            </div>

            <!-- Pass/Fail Trend -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pass Rate Trends</h3>
                <div class="h-64" id="passRateTrendChart"></div>
            </div>
        </div>
    </div>
@endsection
