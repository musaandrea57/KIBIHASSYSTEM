@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('principal.student-performance.index') }}" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-300 mr-4"></i>
                        <a href="{{ route('principal.student-performance.at-risk') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Students</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-300 mr-4"></i>
                        <span class="text-sm font-medium text-gray-900" aria-current="page">Profile</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Identity Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 p-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center">
                    <span class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-2xl font-bold">
                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                    </span>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</h1>
                        <div class="flex flex-wrap gap-4 mt-1 text-sm text-gray-500">
                            <span class="flex items-center"><i class="fas fa-id-card mr-1"></i> {{ $student->registration_number }}</span>
                            @if($student->nactvet_registration_number)
                                <span class="flex items-center"><i class="fas fa-certificate mr-1"></i> {{ $student->nactvet_registration_number }}</span>
                            @endif
                            <span class="flex items-center"><i class="fas fa-layer-group mr-1"></i> Level {{ $student->current_nta_level }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex flex-col items-end">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Active
                    </span>
                    <span class="text-sm text-gray-500 mt-1">{{ $student->program->name ?? 'Unknown Program' }}</span>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-100 pt-4 flex flex-wrap gap-4">
                <a href="{{ route('principal.student-performance.export', ['report' => 'student', 'student_id' => $student->id, 'format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Download Profile
                </a>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase">Credits Earned</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ $results->where('grade_point', '>=', 2.0)->sum('moduleOffering.module.credits') }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase">Cumulative GPA</p>
                @php
                    $totalPoints = 0;
                    $totalWeights = 0;
                    foreach($results as $res) {
                        // Assuming simple GPA calculation: sum(grade_point * credits) / sum(credits)
                        // This is an approximation if strict CGPA logic is complex
                        $credits = $res->moduleOffering->module->credits ?? 0;
                        $totalPoints += $res->grade_point * $credits;
                        $totalWeights += $credits;
                    }
                    $cgpa = $totalWeights > 0 ? round($totalPoints / $totalWeights, 2) : 0;
                @endphp
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $cgpa }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase">Failed Modules</p>
                <p class="text-2xl font-bold text-red-600 mt-1">
                    {{ $results->where('total_mark', '<', 40)->count() }}
                </p>
            </div>
        </div>

        <!-- Results History -->
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-gray-900">Academic History</h2>
            
            @php
                $groupedResults = $results->groupBy(function($item) {
                    return $item->academicYear->name . ' - ' . $item->semester->name;
                });
            @endphp

            @foreach($groupedResults as $semesterName => $semesterResults)
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">{{ $semesterName }}</h3>
                    @php
                        // Semester GPA
                        $semPoints = 0;
                        $semWeights = 0;
                        foreach($semesterResults as $res) {
                            $credits = $res->moduleOffering->module->credits ?? 0;
                            $semPoints += $res->grade_point * $credits;
                            $semWeights += $credits;
                        }
                        $semGpa = $semWeights > 0 ? round($semPoints / $semWeights, 2) : 0;
                    @endphp
                    <span class="text-sm font-medium text-gray-600">Semester GPA: <span class="text-gray-900 font-bold">{{ $semGpa }}</span></span>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Name</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($semesterResults as $result)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $result->moduleOffering->module->code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $result->moduleOffering->module->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $result->moduleOffering->module->credits ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                                {{ $result->grade }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $result->grade_point }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                @if($result->total_mark >= 40)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Pass</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Fail</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

            @if($results->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-500">No results found for this student.</p>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
