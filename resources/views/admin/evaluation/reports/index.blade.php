@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-semibold mb-6">Evaluation Reports</h2>

                <form method="GET" action="{{ route('admin.evaluation.reports.index') }}" class="mb-8 p-4 bg-gray-50 rounded">
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Select Period</label>
                            <select name="period_id" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ (isset($selectedPeriod) && $selectedPeriod->id == $period->id) ? 'selected' : '' }}>
                                    {{ $period->academicYear->name }} - {{ $period->semester->name }} ({{ $period->status }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">View Report</button>
                    </div>
                </form>

                @if($reportData)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submissions</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Score (1-5)</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData as $row)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $row['teacher'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $row['code'] }} - {{ $row['module'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $row['submissions'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded {{ $row['overall_score'] >= 4 ? 'bg-green-100 text-green-800' : ($row['overall_score'] >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $row['overall_score'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <!-- Detailed breakdown could be a modal or expand -->
                                    <div class="text-xs">
                                        @foreach($row['question_breakdown'] as $qId => $score)
                                        <div>Q{{$qId}}: {{ $score }}</div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif(isset($selectedPeriod))
                    <p class="text-center text-gray-500 mt-4">No data found for this period.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
