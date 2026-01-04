@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Registration Reports</h1>
                <p class="mt-1 text-sm text-gray-600">Semester registration analysis by programme and NTA level.</p>
            </div>
            <a href="{{ route('principal.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Dashboard
            </a>
        </div>

        @include('principal.reports.partials.filter-bar')

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Registration Summary</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('principal.reports.export', array_merge($filters, ['report' => 'registration', 'format' => 'pdf'])) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        <i class="fas fa-file-pdf mr-1.5"></i> Export PDF
                    </a>
                    <a href="{{ route('principal.reports.export', array_merge($filters, ['report' => 'registration', 'format' => 'excel'])) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                        <i class="fas fa-file-excel mr-1.5"></i> Export Excel
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programme</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NTA Level</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Active</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unregistered</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% Registered</th>
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
                                {{ number_format($row->total_students) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium text-right">
                                {{ number_format($row->registered_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium text-right">
                                {{ number_format($row->unregistered_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row->registration_rate >= 90 ? 'bg-green-100 text-green-800' : ($row->registration_rate >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($row->registration_rate, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">List</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No registration data found for the selected filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
