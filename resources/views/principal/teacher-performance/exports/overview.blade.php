<!DOCTYPE html>
<html>
<head>
    <title>Teacher Performance Overview</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { bg-color: #f2f2f2; }
        .header { margin-bottom: 30px; }
        .meta { font-size: 0.8em; color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Teacher Performance Overview</h1>
        <div class="meta">
            Generated: {{ $generated_at->format('d M Y H:i') }}<br>
            Filters: {{ json_encode($filters) }}
        </div>
    </div>

    <h2>Key Performance Indicators</h2>
    <table>
        <tr>
            <th>Metric</th>
            <th>Value</th>
        </tr>
        <tr><td>Avg Delivery Rate</td><td>{{ number_format($kpis['avg_delivery_rate'], 1) }}%</td></tr>
        <tr><td>Attendance Completion</td><td>{{ number_format($kpis['attendance_completion'], 1) }}%</td></tr>
        <tr><td>On-time Coursework</td><td>{{ number_format($kpis['on_time_coursework'], 1) }}%</td></tr>
        <tr><td>Results Compliance</td><td>{{ number_format($kpis['results_compliance'], 1) }}%</td></tr>
        <tr><td>Avg Evaluation Rating</td><td>{{ number_format($kpis['avg_evaluation'], 1) }} / 5.0</td></tr>
    </table>

    <h2>Teacher Rankings</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Department</th>
                <th>Index</th>
                <th>Delivery</th>
                <th>Rating</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $index => $teacher)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $teacher->name }}</td>
                <td>{{ $teacher->staffProfile->department->name ?? 'N/A' }}</td>
                <td>{{ $teacher->metrics['performance_index'] }}</td>
                <td>{{ $teacher->metrics['delivery_rate'] ?? 'N/A' }}%</td>
                <td>{{ $teacher->metrics['evaluation_rating'] }}</td>
                <td>{{ $teacher->performance_status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
