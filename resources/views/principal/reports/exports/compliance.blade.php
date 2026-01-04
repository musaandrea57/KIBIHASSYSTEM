@extends('principal.reports.exports.layout')

@section('title', 'Compliance & Data Completeness Report')

@section('content')
    <div style="margin-bottom: 30px;">
        <h3>Executive Summary</h3>
        <table style="width: 50%;">
            <tr>
                <th>Metric</th>
                <th class="text-right">Value</th>
            </tr>
            <tr>
                <td>Total Active Students</td>
                <td class="text-right">{{ number_format($data['summary']['total_students']) }}</td>
            </tr>
            <tr>
                <td>Missing NACTVET Registration</td>
                <td class="text-right">{{ number_format($data['summary']['missing_nactvet']) }}</td>
            </tr>
            <tr>
                <td><strong>NACTVET Compliance Rate</strong></td>
                <td class="text-right"><strong>{{ number_format($data['summary']['nactvet_compliance_rate'], 1) }}%</strong></td>
            </tr>
        </table>
    </div>

    <div>
        <h3>Programme Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Programme</th>
                    <th class="text-right">Total Students</th>
                    <th class="text-right">Missing NACTVET</th>
                    <th class="text-right">Compliance Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['breakdown'] as $row)
                <tr>
                    <td>{{ $row->program_code }} - {{ $row->program_name }}</td>
                    <td class="text-right">{{ number_format($row->total_students) }}</td>
                    <td class="text-right">{{ number_format($row->missing_nactvet) }}</td>
                    <td class="text-right">{{ number_format($row->compliance_rate, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="margin-top: 20px;"><em>Note: This report summarizes data completeness for active students only.</em></p>
@endsection