<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Results</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; font-weight: bold; }
        .logo { width: 80px; height: auto; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: center; }
        .text-left { text-align: left !important; }
        .details-table { width: 100%; margin-bottom: 15px; }
        .details-table td { padding: 3px; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; text-decoration: underline; }
        .footer { margin-top: 40px; }
        .signature-line { border-bottom: 1px solid #000; width: 200px; display: inline-block; margin-top: 30px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo"><br>
        MINISTRY OF HEALTH, COMMUNITY DEVELOPMENT,<br>
        GENDER, ELDERLY AND CHILDREN<br>
        KIBOSHO INSTITUTE OF HEALTH AND ALLIED SCIENCES (KIBIHAS)<br><br>
        {{ strtoupper($student->program->name ?? 'PROGRAM') }}<br>
        OVERALL SUMMARY OF RESULTS<br>
        ACADEMIC YEAR: {{ $academicYear->name }}
    </div>

    <div class="section-title">STUDENT DETAILS</div>
    <table class="details-table">
        <tr>
            <td width="25%">Student Name:</td>
            <td class="text-left"><strong>{{ strtoupper($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name) }}</strong></td>
            <td width="25%">Institute Reg No:</td>
            <td class="text-left"><strong>{{ $student->registration_number }}</strong></td>
        </tr>
        <tr>
            <td>NACTVET Reg No:</td>
            <td class="text-left"><strong>{{ $student->nactvet_registration_number ?? 'N/A' }}</strong></td>
            <td>Course:</td>
            <td class="text-left"><strong>{{ $student->program->code ?? '' }}</strong></td>
        </tr>
    </table>

    <div class="section-title">SEMESTER {{ $semester->name }} MODULE RESULTS</div>
    <table class="table">
        <thead>
            <tr>
                <th>Module Code</th>
                <th>Module Name</th>
                <th>CW</th>
                <th>SE</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Credits</th>
                <th>Points</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr>
                <td class="text-left">{{ $result->moduleOffering->module->code ?? '' }}</td>
                <td class="text-left">{{ $result->moduleOffering->module->name ?? '' }}</td>
                <td>{{ $result->cw_mark }}</td>
                <td>{{ $result->se_mark }}</td>
                <td>{{ $result->total_mark }}</td>
                <td>{{ $result->grade }}</td>
                <td>{{ $result->credits_snapshot }}</td>
                <td>{{ $result->points }}</td>
                <td>{{ $result->remark }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">SEMESTER PERFORMANCE SUMMARY</div>
    <table class="details-table" style="width: 50%">
        <tr>
            <td>Total Credits:</td>
            <td><strong>{{ $summary['total_credits'] }}</strong></td>
        </tr>
        <tr>
            <td>Total Points:</td>
            <td><strong>{{ $summary['total_points'] }}</strong></td>
        </tr>
        <tr>
            <td>Grade Point Average (GPA):</td>
            <td><strong>{{ $summary['gpa'] }}</strong></td>
        </tr>
        <tr>
            <td>Overall Classification:</td>
            <td><strong>{{ strtoupper($summary['classification']) }}</strong></td>
        </tr>
    </table>

    <div class="section-title">GRADING SYSTEM SUMMARY</div>
    <table class="table" style="width: 80%">
        <thead>
            <tr>
                <th>SN</th>
                <th>Marks Range</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Definition</th>
            </tr>
        </thead>
        <tbody>
            @php $scales = \App\Models\GradeScale::orderBy('min_mark', 'desc')->get(); @endphp
            @foreach($scales as $index => $scale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $scale->min_mark }} - {{ $scale->max_mark }}</td>
                <td>{{ $scale->grade }}</td>
                <td>{{ $scale->grade_point }}</td>
                <td class="text-left">{{ $scale->definition }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%">
            <tr>
                <td style="text-align: center">
                    ..................................................<br>
                    <strong>ACADEMIC OFFICER</strong>
                </td>
                <td style="text-align: center">
                    ..................................................<br>
                    <strong>PRINCIPAL</strong>
                </td>
            </tr>
        </table>
        <div style="margin-top: 20px; font-size: 10px; font-style: italic;">
            Printed on: {{ now()->format('d-m-Y H:i:s') }}
        </div>
    </div>
</body>
</html>
