@extends('layouts.portal')

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Evaluation Dashboard
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Overview of the current evaluation period.
        </p>
    </div>
    
    <div class="border-t border-gray-200">
        <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Active Period</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    @if($activePeriod)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $activePeriod->name }} ({{ $activePeriod->start_date->format('d M') }} - {{ $activePeriod->end_date->format('d M Y') }})
                        </span>
                    @else
                        <span class="text-gray-500 italic">No active evaluation period.</span>
                    @endif
                </dd>
            </div>
            
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Participation Rate</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2 max-w-xs">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $participationRate }}%"></div>
                        </div>
                        <span>{{ $participationRate }}% ({{ $submittedEvaluations }} / {{ $totalEvaluations }})</span>
                    </div>
                </dd>
            </div>

            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Evaluation Status</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <ul class="list-disc pl-5">
                        <li>Submitted: {{ $submittedEvaluations }}</li>
                        <li>Pending: {{ $pendingEvaluations }}</li>
                    </ul>
                </dd>
            </div>
        </dl>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Recent Submissions
        </h3>
    </div>
    <ul class="divide-y divide-gray-200">
        @forelse($recentSubmissions as $evaluation)
            <li class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-indigo-600 truncate">
                        {{ $evaluation->moduleOffering->module->code }} - {{ $evaluation->moduleOffering->module->name }}
                    </p>
                    <div class="ml-2 flex-shrink-0 flex">
                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Submitted
                        </p>
                    </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                        <p class="flex items-center text-sm text-gray-500">
                            Lecturer: {{ $evaluation->teacher->name }}
                        </p>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                        <p>
                            {{ $evaluation->submitted_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 sm:px-6 text-gray-500 italic">No recent submissions.</li>
        @endforelse
    </ul>
</div>
@endsection
