@extends('principal.reports.exports.layout')

@section('title', $title)

@section('content')
    <!-- KPIs -->
    <h3>Executive Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Students</td>
                <td class="text-right">{{ number_format($data['kpis']['total_students']) }}</td>
            </tr>
            <tr>
                <td>Average GPA</td>
                <td class="text-right">{{ $data['kpis']['avg_gpa'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Pass Rate</td>
                <td class="text-right">{{ $data['kpis']['pass_rate'] !== null ? $data['kpis']['pass_rate'] . '%' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Fail Rate</td>
                <td class="text-right">{{ $data['kpis']['fail_rate'] !== null ? $data['kpis']['fail_rate'] . '%' : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Fail/Carry Count</td>
                <td class="text-right">{{ number_format($data['kpis']['carry_count']) }}</td>
            </tr>
            <tr>
                <td>Published Results Coverage</td>
                <td class="text-right">{{ $data['kpis']['published_coverage'] }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Data Quality -->
    @if(array_sum($data['kpis']['data_quality']) > 0)
    <div style="background-color: #fff7ed; border: 1px solid #fdba74; padding: 10px; margin-bottom: 20px;">
        <h4 style="margin-top: 0; color: #c2410c;">Data Quality Warnings</h4>
        <ul>
            @if($data['kpis']['data_quality']['missing_nactvet'] > 0)
                <li>{{ $data['kpis']['data_quality']['missing_nactvet'] }} students missing NACTVET registration numbers.</li>
            @endif
            @if($data['kpis']['data_quality']['missing_registration'] > 0)
                <li>{{ $data['kpis']['data_quality']['missing_registration'] }} active students missing semester registration.</li>
            @endif
            @if($data['kpis']['data_quality']['missing_results'] > 0)
                <li>{{ $data['kpis']['data_quality']['missing_results'] }} registered students missing published results.</li>
            @endif
        </ul>
    </div>
    @endif

    <!-- Distributions -->
    <h3>Grade Distribution</h3>
    <table>
        <thead>
            <tr>
                <th>Grade</th>
                <th class="text-right">Count</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGrades = array_sum($data['distributions']['grades']); @endphp
            @foreach($data['distributions']['grades'] as $grade => $count)
            <tr>
                <td>{{ $grade }}</td>
                <td class="text-right">{{ number_format($count) }}</td>
                <td class="text-right">{{ $totalGrades > 0 ? round(($count / $totalGrades) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>GPA Distribution</h3>
    <table>
        <thead>
            <tr>
                <th>GPA Band</th>
                <th class="text-right">Count</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGpa = array_sum($data['distributions']['gpa_bands']); @endphp
            @foreach($data['distributions']['gpa_bands'] as $band => $count)
            <tr>
                <td>{{ $band }}</td>
                <td class="text-right">{{ number_format($count) }}</td>
                <td class="text-right">{{ $totalGpa > 0 ? round(($count / $totalGpa) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
