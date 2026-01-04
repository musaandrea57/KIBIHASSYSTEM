@extends('principal.reports.exports.layout')

@section('title', $title)

@section('content')
    <table>
        <thead>
            <tr>
                <th>Programme</th>
                <th class="text-right">Students</th>
                <th class="text-right">Avg GPA</th>
                <th class="text-right">Pass Rate</th>
                <th>Strongest Module</th>
                <th>Weakest Module</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>
                    <strong>{{ $row['program_code'] }}</strong><br>
                    <small>{{ $row['program_name'] }}</small>
                </td>
                <td class="text-right">{{ number_format($row['student_count']) }}</td>
                <td class="text-right">{{ $row['avg_gpa'] ?? 'N/A' }}</td>
                <td class="text-right">{{ $row['pass_rate'] !== null ? $row['pass_rate'] . '%' : 'N/A' }}</td>
                <td>{{ $row['strongest_module'] }}</td>
                <td>{{ $row['weakest_module'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
