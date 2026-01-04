@extends('principal.reports.exports.layout')

@section('title', 'Registration Summary Report')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Programme</th>
                <th>NTA Level</th>
                <th class="text-right">Total Active</th>
                <th class="text-right">Registered</th>
                <th class="text-right">Unregistered</th>
                <th class="text-right">% Registered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->program_code }} - {{ $row->program_name }}</td>
                <td>Level {{ $row->current_nta_level }}</td>
                <td class="text-right">{{ number_format($row->total_students) }}</td>
                <td class="text-right">{{ number_format($row->registered_count) }}</td>
                <td class="text-right">{{ number_format($row->unregistered_count) }}</td>
                <td class="text-right">{{ number_format($row->registration_rate, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <h3>Summary</h3>
        <p>
            Total Programmes: {{ $data->count() }}<br>
            Total Students: {{ $data->sum('total_students') }}<br>
            Total Registered: {{ $data->sum('registered_count') }}<br>
            Overall Registration Rate: {{ $data->sum('total_students') > 0 ? number_format(($data->sum('registered_count') / $data->sum('total_students')) * 100, 1) : 0 }}%
        </p>
    </div>
@endsection
