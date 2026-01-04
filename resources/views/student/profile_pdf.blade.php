<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Profile - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #003366;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #003366;
            background-color: #f0f4f8;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #003366;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: bold;
            color: #555;
            width: 35%;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .photo-container {
            float: right;
            width: 120px;
            height: 120px;
            margin-left: 20px;
            border: 1px solid #ddd;
            padding: 3px;
        }
        .photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">KIBIHAS INSTITUTE</div>
        <div class="subtitle">Student Profile Record</div>
    </div>

    <div class="section clearfix">
        @if($student->profile_photo_path)
            <div class="photo-container">
                <!-- Using absolute path for dompdf -->
                <img src="{{ storage_path('app/public/' . $student->profile_photo_path) }}" class="photo">
            </div>
        @endif

        <div style="float: left; width: {{ $student->profile_photo_path ? '70%' : '100%' }};">
            <table>
                <tr>
                    <th>Full Name</th>
                    <td>{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <th>Registration Number</th>
                    <td>{{ $student->registration_number }}</td>
                </tr>
                <tr>
                    <th>Program</th>
                    <td>{{ $student->program->name }}</td>
                </tr>
                <tr>
                    <th>Current Level</th>
                    <td>NTA Level {{ $student->current_nta_level }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Personal Information</div>
        <table>
            <tr>
                <th>Email Address</th>
                <td>{{ $student->user->email }}</td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>{{ $student->phone ?? 'Not Provided' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ ucfirst($student->gender) }}</td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Academic Status</div>
        <table>
            <tr>
                <th>Admission Year</th>
                <td>{{ $student->admission_year }}</td>
            </tr>
            <tr>
                <th>Current Academic Year</th>
                <td>{{ $student->currentAcademicYear->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Current Semester</th>
                <td>{{ $student->currentSemester->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($student->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | KIBIHAS Portal
    </div>
</body>
</html>
