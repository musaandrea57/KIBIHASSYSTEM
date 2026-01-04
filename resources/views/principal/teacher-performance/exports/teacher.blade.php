<!DOCTYPE html>
<html>
<head>
    <title>Teacher Scorecard: {{ $teacher->name }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; text-transform: uppercase; color: #555; }
        .kpi-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; display: inline-block; width: 45%; margin-right: 2%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Teacher Scorecard</h1>
        <h3>{{ $teacher->name }} ({{ $teacher->staffProfile->department->name ?? 'Unassigned' }})</h3>
        <p>Performance Index: <strong>{{ $metrics['performance_index'] }}</strong> ({{ $teacher->performance_status }})</p>
    </div>

    <div class="section-title">Performance Metrics</div>
    <table>
        <tr>
            <th>Metric</th>
            <th>Value</th>
            <th>Weight</th>
        </tr>
        <tr>
            <td>Delivery Rate</td>
            <td>{{ $metrics['delivery_rate'] ?? 'N/A' }}%</td>
            <td>{{ config('performance.weights.delivery_rate') }}%</td>
        </tr>
        <tr>
            <td>Attendance Completion</td>
            <td>{{ $metrics['attendance_completion'] ?? 'N/A' }}%</td>
            <td>{{ config('performance.weights.attendance_completion') }}%</td>
        </tr>
        <tr>
            <td>Assessment Timeliness</td>
            <td>{{ $metrics['upload_timeliness'] }}%</td>
            <td>{{ config('performance.weights.assessment_timeliness') }}%</td>
        </tr>
        <tr>
            <td>Student Evaluation</td>
            <td>{{ $metrics['evaluation_rating'] }} / 5.0</td>
            <td>{{ config('performance.weights.evaluation_rating') }}%</td>
        </tr>
        <tr>
            <td>Results Compliance</td>
            <td>{{ $metrics['results_compliance'] }}%</td>
            <td>{{ config('performance.weights.results_compliance') }}%</td>
        </tr>
    </table>

    <div class="section-title">Peer Comparison</div>
    @if($peers)
    <table>
        <tr>
            <th>Context</th>
            <th>Index</th>
            <th>Delivery Avg</th>
        </tr>
        <tr>
            <td>Teacher</td>
            <td>{{ $metrics['performance_index'] }}</td>
            <td>{{ $metrics['delivery_rate'] ?? 'N/A' }}%</td>
        </tr>
        <tr>
            <td>Department Avg</td>
            <td>{{ $peers['department_avg_index'] }}</td>
            <td>{{ $peers['department_avg_delivery'] }}%</td>
        </tr>
        <tr>
            <td>Institution Avg</td>
            <td>{{ $peers['institution_avg_index'] }}</td>
            <td>{{ $peers['institution_avg_delivery'] }}%</td>
        </tr>
    </table>
    @endif

    <div class="section-title">Assigned Modules</div>
    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Code</th>
                <th>Academic Year</th>
                <th>Semester</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $assignment)
            <tr>
                <td>{{ $assignment->moduleOffering->module->name ?? 'N/A' }}</td>
                <td>{{ $assignment->moduleOffering->module->code ?? 'N/A' }}</td>
                <td>{{ $assignment->academicYear->name ?? 'N/A' }}</td>
                <td>{{ $assignment->semester->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Recent Evaluations</div>
    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Rating</th>
                <th>Responses</th>
                <th>Confidence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evidence['evaluations'] as $eval)
            <tr>
                <td>{{ $eval->moduleOffering->module->name ?? 'N/A' }}</td>
                <td>{{ number_format($eval->answers->avg('rating'), 1) }}</td>
                <td>{{ $eval->response_count }}</td>
                <td>{{ $eval->confidence }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
