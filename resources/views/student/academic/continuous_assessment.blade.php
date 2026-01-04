@extends('layouts.portal')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Continuous Assessment</h1>
        <p class="mt-1 text-sm text-gray-600">
            {{ $activeYear->year }} - {{ $activeSemester->name }}
        </p>
    </div>

    @if($results->isEmpty())
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
            <p class="text-gray-500">No coursework results available for this semester.</p>
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($results as $result)
                    @php
                        // Check if coursework is released for this module offering
                        // Accessing via relationship if eager loaded, or check property
                        $isReleased = $result->moduleOffering->coursework_released ?? false;
                    @endphp
                    <li class="p-4 sm:px-6">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-lg font-medium text-blue-600">
                                {{ $result->moduleOffering->module->code }} - {{ $result->moduleOffering->module->name }}
                            </div>
                            @if(!$isReleased)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Results Not Released
                                </span>
                            @endif
                        </div>

                        @if($isReleased)
                            @if($result->continuousAssessments->isEmpty())
                                <p class="text-sm text-gray-500 italic">No marks recorded yet.</p>
                            @else
                                <div class="mt-2 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max</th>
                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($result->continuousAssessments as $assessment)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $assessment->assessmentType->name ?? 'Assessment' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                                        {{ $assessment->mark }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $assessment->max_mark }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $assessment->max_mark > 0 ? number_format(($assessment->mark / $assessment->max_mark) * 100, 1) . '%' : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <!-- Total CA Row -->
                                            <tr class="bg-gray-50 font-semibold">
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">Total Coursework</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-blue-600">
                                                    {{ $result->cw_mark }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <!-- Max CA not always clear, assume standard or sum -->
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">
                                Coursework results for this module have not been released to students yet.
                            </p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
