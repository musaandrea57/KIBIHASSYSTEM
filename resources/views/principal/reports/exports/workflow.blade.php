@extends('principal.reports.exports.layout')

@section('title', 'Assessments Workflow Report')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Module Code</th>
                <th>Module Name</th>
                <th class="text-right">Students</th>
                <th>Status</th>
                <th class="text-right">Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['module_code'] }}</td>
                <td>{{ $row['module_name'] }}</td>
                <td class="text-right">{{ number_format($row['total_students']) }}</td>
                <td>{{ $row['status'] }}</td>
                <td class="text-right">{{ $row['last_updated'] ? \Carbon\Carbon::parse($row['last_updated'])->format('d M Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
