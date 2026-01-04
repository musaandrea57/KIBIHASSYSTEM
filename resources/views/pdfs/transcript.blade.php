<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Academic Transcript</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; font-weight: bold; }
        .logo { width: 60px; height: auto; margin-bottom: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .text-left { text-align: left !important; }
        .details-table { width: 100%; margin-bottom: 15px; }
        .details-table td { padding: 2px; }
        .semester-header { background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 5px; border: 1px solid #000; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" class="logo" alt="Logo"><br>
        KIBOSHO INSTITUTE OF HEALTH AND ALLIED SCIENCES (KIBIHAS)<br>
        ACADEMIC TRANSCRIPT
    </div>

    <table class="details-table">
        <tr>
            <td width="20%"><strong>Name:</strong></td>
            <td>{{ strtoupper($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name) }}</td>
            <td width="20%"><strong>Reg No:</strong></td>
            <td>{{ $student->registration_number }}</td>
        </tr>
        <tr>
            <td><strong>Program:</strong></td>
            <td>{{ $student->program->name ?? '' }}</td>
            <td><strong>NTA Level:</strong></td>
            <td>{{ $student->current_nta_level ?? '' }}</td>
        </tr>
    </table>

    @foreach($results as $yearId => $yearGroup)
        @foreach($yearGroup as $semesterId => $semesterResults)
            @php 
                $year = $semesterResults->first()->academicYear;
                $semester = $semesterResults->first()->semester;
            @endphp
            
            <div class="semester-header">
                Academic Year: {{ $year->name }} | Semester: {{ $semester->name }}
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th width="15%">Code</th>
                        <th width="40%">Module Name</th>
                        <th width="10%">Credits</th>
                        <th width="10%">Grade</th>
                        <th width="10%">Points</th>
                        <th width="15%">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semesterResults as $result)
                    <tr>
                        <td class="text-left">{{ $result->moduleOffering->module->code ?? '' }}</td>
                        <td class="text-left">{{ $result->moduleOffering->module->name ?? '' }}</td>
                        <td>{{ $result->credits_snapshot }}</td>
                        <td>{{ $result->grade }}</td>
                        <td>{{ $result->points }}</td>
                        <td>{{ $result->remark }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach

    <div style="margin-top: 30px;">
        <p><strong>Issued by:</strong> ........................................................... Date: {{ date('d-m-Y') }}</p>
    </div>
</body>
</html>
