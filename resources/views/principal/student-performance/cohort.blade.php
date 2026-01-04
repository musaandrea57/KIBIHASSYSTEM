@extends('layouts.portal')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Cohort Performance</h1>
        <p class="mt-2 text-sm text-gray-600">Longitudinal analysis by student intake year.</p>
    </div>

    <!-- Filter Bar -->
    @include('principal.student-performance.partials.filter-bar')

    <!-- Navigation -->
    @include('principal.student-performance.partials.nav')

    <!-- Data Quality Alert -->
    @include('principal.student-performance.partials.data-quality-alert', ['kpis' => $kpis])

    <!-- Main Content -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Cohort Summary</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cohort Year</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Active Students</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Retention Rate</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg GPA</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $row)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $row['cohort_year'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ number_format($row['total_students']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ number_format($row['active_students']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                <div class="flex items-center justify-center">
                                    <span class="mr-2">{{ number_format($row['retention_rate'], 1) }}%</span>
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $row['retention_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($row['avg_gpa'] !== null)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row['avg_gpa'] >= 3.5 ? 'bg-green-100 text-green-800' : ($row['avg_gpa'] >= 2.0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($row['avg_gpa'], 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                @if($row['pass_rate'] !== null)
                                    <div class="flex items-center justify-center">
                                        <span class="mr-2">{{ number_format($row['pass_rate'], 1) }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $row['pass_rate'] }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-gray-100 rounded-full mb-3">
                                        <i class="fas fa-user-friends text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">No Cohort Data</h3>
                                    <p class="text-sm text-gray-500 mt-1">No cohort records found for the selected criteria.</p>
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
