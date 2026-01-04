@extends('principal.reports.exports.layout')

@section('title', 'Progression & Retention Report')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Programme</th>
                <th>Status</th>
                <th class="text-right">Student Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->program_name }}</td>
                <td>{{ ucfirst($row->status) }}</td>
                <td class="text-right">{{ number_format($row->count) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
