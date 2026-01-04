@if(isset($kpis['data_quality']) && array_sum($kpis['data_quality']) > 0)
<div class="mb-8 bg-amber-50 rounded-xl border border-amber-200 p-4 shadow-sm animate-pulse-slow">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-1"></i>
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-sm font-bold text-amber-800">Data Quality Alert: Metrics Suppressed or Incomplete</h3>
            <p class="text-sm text-amber-700 mt-1">
                The following data quality issues were detected. Dependent KPIs have been suppressed or marked as provisional to prevent misleading decision-making.
            </p>
            <div class="mt-3 text-sm text-amber-800 bg-amber-100 rounded-lg p-3">
                <ul class="list-disc pl-5 space-y-1">
                    @if($kpis['data_quality']['missing_nactvet'] > 0)
                        <li>
                            <span class="font-bold">{{ $kpis['data_quality']['missing_nactvet'] }} students</span> are missing NACTVET registration numbers.
                        </li>
                    @endif
                    @if($kpis['data_quality']['missing_registration'] > 0)
                        <li>
                            <span class="font-bold">{{ $kpis['data_quality']['missing_registration'] }} active students</span> are not registered for the selected academic period.
                        </li>
                    @endif
                    @if($kpis['data_quality']['missing_results'] > 0)
                        <li>
                            <span class="font-bold">{{ $kpis['data_quality']['missing_results'] }} registered students</span> have no published results. 
                            <span class="text-amber-600 italic">(Results must be published to appear in analytics)</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
