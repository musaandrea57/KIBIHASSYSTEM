@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Compliance Reports</h1>
                <p class="mt-1 text-sm text-gray-600">NACTVET registration and data completeness analysis.</p>
            </div>
            <a href="{{ route('principal.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Back to Dashboard
            </a>
        </div>

        @include('principal.reports.partials.filter-bar')

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                 <h3 class="text-lg font-medium text-gray-900">Data Completeness Overview</h3>
                 <div class="flex space-x-2">
                    <a href="{{ route('principal.reports.export', array_merge($filters, ['report' => 'compliance', 'format' => 'pdf'])) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        <i class="fas fa-file-pdf mr-1.5"></i> Export PDF
                    </a>
                    <a href="{{ route('principal.reports.export', array_merge($filters, ['report' => 'compliance', 'format' => 'xlsx'])) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                        <i class="fas fa-file-excel mr-1.5"></i> Export Excel
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                @php
                    $summary = $reportData['summary'];
                    $breakdown = $reportData['breakdown'];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-1">Total Active Students</h4>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($summary['total_students']) }}</p>
                    </div>
                    
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-red-800 mb-1">Missing NACTVET Reg No.</h4>
                        <p class="text-2xl font-bold text-red-900">{{ number_format($summary['missing_nactvet']) }}</p>
                        <p class="text-xs text-red-600 mt-1">
                            {{ $summary['total_students'] > 0 ? round(($summary['missing_nactvet'] / $summary['total_students']) * 100, 1) : 0 }}% of students
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                     <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Compliance Status</h4>
                     <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                    NACTVET Coverage
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-green-600">
                                    {{ number_format($summary['nactvet_compliance_rate'], 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                            <div style="width:{{ $summary['nactvet_compliance_rate'] }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Programme Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Programme
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Students
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Missing NACTVET
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Compliance Rate
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($breakdown as $row)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $row->program_code }} - {{ $row->program_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ number_format($row->total_students) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right font-medium">
                                {{ number_format($row->missing_nactvet) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($row->compliance_rate >= 90)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ number_format($row->compliance_rate, 1) }}%
                                    </span>
                                @elseif($row->compliance_rate >= 70)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ number_format($row->compliance_rate, 1) }}%
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ number_format($row->compliance_rate, 1) }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection