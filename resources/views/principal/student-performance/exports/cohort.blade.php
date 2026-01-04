@extends('principal.reports.exports.layout')

@section('title', $title)

@section('content')
    <table>
        <thead>
            <tr>
                <th>Cohort / Intake Year</th>
                <th class="text-right">Total Enrolled</th>
                <th class="text-right">Active Students</th>
                <th class="text-right">Retention Rate</th>
                <th class="text-right">Avg GPA</th>
                <th class="text-right">Pass Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['cohort_year'] }}</td>
                <td class="text-right">{{ number_format($row['total_students']) }}</td>
                <td class="text-right">{{ number_format($row['active_students']) }}</td>
                <td class="text-right">{{ $row['retention_rate'] }}%</td>
                <td class="text-right">{{ $row['avg_gpa'] ?? 'N/A' }}</td>
                <td class="text-right">{{ $row['pass_rate'] !== null ? $row['pass_rate'] . '%' : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
