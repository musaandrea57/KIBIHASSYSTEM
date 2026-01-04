@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Performance Reports</h1>
                <p class="mt-1 text-sm text-gray-600">Detailed analysis of academic performance across programmes and modules.</p>
            </div>
            <a href="{{ route('principal.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Dashboard
            </a>
        </div>

        <!-- Filter Bar -->
        @include('principal.reports.partials.filter-bar')

        <!-- Report Type Navigation -->
        <div class="bg-white px-4 rounded-t-lg border-b border-gray-200 mb-0">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <a href="{{ route('principal.reports.performance', array_merge($filters, ['type' => 'programme_summary'])) }}" 
                   class="{{ $reportType == 'programme_summary' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Programme Summary
                </a>
                <a href="{{ route('principal.reports.performance', array_merge($filters, ['type' => 'module_performance'])) }}" 
                   class="{{ $reportType == 'module_performance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Module Performance
                </a>
                <a href="{{ route('principal.reports.performance', array_merge($filters, ['type' => 'student_listing'])) }}" 
                   class="{{ $reportType == 'student_listing' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Student Listing
                </a>
            </nav>
        </div>

        <!-- Report Content -->
        <div class="bg-white shadow-sm rounded-b-lg border border-gray-200 border-t-0 p-6">
            
            <!-- Metadata Header -->
            <div class="flex justify-between items-end mb-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        @if($reportType == 'programme_summary') Programme Performance Summary
                        @elseif($reportType == 'module_performance') Module Performance Analysis
                        @else Student Performance Listing
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Generated on {{ now()->format('d M Y, H:i') }} by {{ auth()->user()->name }}</p>
                </div>
                <div class="text-right text-xs text-gray-500">
                    @if(!empty($filters['academic_year_id']))
                        <span class="bg-gray-100 px-2 py-1 rounded">AY: {{ $filterOptions['academic_years']->find($filters['academic_year_id'])->name ?? 'N/A' }}</span>
                    @endif
                    @if(!empty($filters['semester_id']))
                        <span class="bg-gray-100 px-2 py-1 rounded ml-1">Sem: {{ $filterOptions['semesters']->find($filters['semester_id'])->name ?? 'N/A' }}</span>
                    @endif
                </div>
            </div>

            @if($reportType == 'programme_summary')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programme</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NTA Level</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Results</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Mark</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg GPA</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reportData as $row)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $row->program_code }} - {{ $row->program_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Level {{ $row->current_nta_level }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($row->total_results) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row->pass_rate >= 80 ? 'bg-green-100 text-green-800' : ($row->pass_rate >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($row->pass_rate, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{ number_format($row->avg_mark, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{ number_format($row->avg_gpa, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Details</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No data found matching the selected filters.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-tools text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Under Construction</h3>
                    <p class="text-gray-500 mt-1">This report view is currently being implemented.</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
