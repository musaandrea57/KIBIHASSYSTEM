@extends('principal.reports.exports.layout')

@section('title', 'Programme Performance Summary')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Programme</th>
                <th>NTA Level</th>
                <th class="text-right">Total Results</th>
                <th class="text-right">Pass Rate</th>
                <th class="text-right">Avg Mark</th>
                <th class="text-right">Avg GPA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->program_code }} - {{ $row->program_name }}</td>
                <td>Level {{ $row->current_nta_level }}</td>
                <td class="text-right">{{ number_format($row->total_results) }}</td>
                <td class="text-right">
                    {{ number_format($row->pass_rate, 1) }}%
                </td>
                <td class="text-right">{{ number_format($row->avg_mark, 1) }}</td>
                <td class="text-right">{{ number_format($row->avg_gpa, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <h3>Analysis Summary</h3>
        <p>
            Total Programmes Analyzed: {{ $data->count() }}<br>
            Average Pass Rate: {{ $data->count() > 0 ? number_format($data->avg('pass_rate'), 1) : 0 }}%
        </p>
    </div>
@endsection
