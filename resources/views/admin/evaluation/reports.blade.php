@extends('layouts.portal')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Evaluation Reports</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Select a period to view aggregated results.
                </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('admin.evaluation.reports') }}" method="GET">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="period_id" class="block text-sm font-medium text-gray-700">Evaluation Period</label>
                            <select id="period_id" name="period_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}" {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }} ({{ $period->start_date->format('d M') }} - {{ $period->end_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-2 flex items-end">
                            <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($results->isNotEmpty())
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Aggregated Results
                </h3>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responses</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Rating</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $result->teacher_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $result->module_code }} - {{ $result->module_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $result->response_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold {{ $result->average_rating >= 4 ? 'text-green-600' : ($result->average_rating >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($result->average_rating, 2) }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-1">/ 5</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button onclick="toggleDetails('{{ $result->teacher_id }}-{{ $result->module_id }}')" class="text-indigo-600 hover:text-indigo-900">
                                        View Breakdown
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Detailed Breakdown Row -->
                            <tr id="details-{{ $result->teacher_id }}-{{ $result->module_id }}" class="hidden bg-gray-50">
                                <td colspan="5" class="px-6 py-4">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Question Breakdown</div>
                                    <div class="grid grid-cols-1 gap-2">
                                        @if(isset($questionStats[$result->teacher_id . '-' . $result->module_id]))
                                            @foreach($questionStats[$result->teacher_id . '-' . $result->module_id] as $stat)
                                                <div class="flex items-center justify-between border-b border-gray-200 pb-1">
                                                    <span class="text-sm text-gray-700 flex-1">{{ $stat->question_text }}</span>
                                                    <span class="text-sm font-medium text-gray-900 ml-4">
                                                        {{ number_format($stat->question_average, 2) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-500 italic">No detailed stats available.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">No data available</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500 mx-auto">
                    <p>No submitted evaluations found for the selected period.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function toggleDetails(id) {
        const row = document.getElementById('details-' + id);
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    }
</script>
@endsection
