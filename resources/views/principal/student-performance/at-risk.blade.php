@extends('layouts.portal')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">At-Risk Students</h1>
        <p class="mt-2 text-sm text-gray-600">Students requiring academic intervention based on performance indicators.</p>
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
            <h2 class="text-lg font-semibold text-gray-800">At-Risk Student List</h2>
            <div class="text-sm text-gray-500">
                Found {{ count($students) }} students at risk
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programme / NTA</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GPA</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Fails</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Factors</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $student['registration_number'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student['program_code'] }}</div>
                                <div class="text-sm text-gray-500">Level {{ $student['nta_level'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student['gpa'] < 2.0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ number_format($student['gpa'], 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $student['fails'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($student['reasons'] as $reason)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $reason == 'Low GPA' || $reason == 'Multiple Fails' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('principal.student-performance.show', $student['id']) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View Profile <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-green-50 rounded-full mb-3">
                                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">No At-Risk Students</h3>
                                    <p class="text-sm text-gray-500 mt-1">Great news! No students meet the risk criteria for the selected period.</p>
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
