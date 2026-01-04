<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Application Form - {{ $application->application_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .university-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .col {
            display: table-cell;
            padding-right: 10px;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 150px;
        }
        .value {
            color: #000;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .passport-photo {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 120px;
            height: 150px;
            border: 1px solid #ccc;
            text-align: center;
            line-height: 150px;
            background-color: #f9f9f9;
        }
        .passport-photo img {
            max-width: 100%;
            max-height: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="university-name">Kibaha Health & Allied Sciences</div>
        <div class="document-title">Student Application Form</div>
        <div>{{ $application->academicYear->name ?? 'Academic Year' }}</div>
    </div>

    <!-- Passport Photo Placeholder -->
    <div class="passport-photo">
        @php
            $photoDoc = $application->uploadedDocuments()->where('type', 'passport_photo')->first();
        @endphp
        @if($photoDoc && Storage::disk('public')->exists($photoDoc->path))
            <img src="{{ public_path('storage/' . $photoDoc->path) }}" alt="Passport Photo">
        @else
            Passport Photo
        @endif
    </div>

    <div class="section">
        <div class="section-title">Application Details</div>
        <div class="row">
            <div class="col label">Application No:</div>
            <div class="col value">{{ $application->application_number }}</div>
        </div>
        <div class="row">
            <div class="col label">Programme:</div>
            <div class="col value">{{ $application->program->name ?? 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="col label">Application Date:</div>
            <div class="col value">{{ $application->submitted_at ? $application->submitted_at->format('d M Y') : 'Draft' }}</div>
        </div>
        <div class="row">
            <div class="col label">Status:</div>
            <div class="col value">{{ ucfirst($application->status) }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="row">
            <div class="col label">Full Name:</div>
            <div class="col value">{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</div>
        </div>
        <div class="row">
            <div class="col label">Gender:</div>
            <div class="col value">{{ ucfirst($application->gender) }}</div>
        </div>
        <div class="row">
            <div class="col label">Date of Birth:</div>
            <div class="col value">{{ $application->date_of_birth ? \Carbon\Carbon::parse($application->date_of_birth)->format('d M Y') : 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="col label">Nationality:</div>
            <div class="col value">{{ $application->nationality }}</div>
        </div>
        <div class="row">
            <div class="col label">NIDA Number:</div>
            <div class="col value">{{ $application->nida_number ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="row">
            <div class="col label">Email:</div>
            <div class="col value">{{ $application->email }}</div>
        </div>
        <div class="row">
            <div class="col label">Phone:</div>
            <div class="col value">{{ $application->phone }}</div>
        </div>
        <div class="row">
            <div class="col label">Address:</div>
            <div class="col value">{{ $application->postal_address }}</div>
        </div>
        <div class="row">
            <div class="col label">Region/District:</div>
            <div class="col value">{{ $application->region }}, {{ $application->district }}</div>
        </div>
    </div>

    @if($application->education_background)
    <div class="section">
        <div class="section-title">Education Background</div>
        <table>
            <thead>
                <tr>
                    <th>Level</th>
                    <th>School/Institution</th>
                    <th>Year Completed</th>
                    <th>Index/Reg Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($application->education_background as $edu)
                <tr>
                    <td>{{ $edu['level'] ?? 'N/A' }}</td>
                    <td>{{ $edu['institution_name'] ?? 'N/A' }}</td>
                    <td>{{ $edu['completion_year'] ?? 'N/A' }}</td>
                    <td>{{ $edu['index_number'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Guardian Information</div>
        <div class="row">
            <div class="col label">Guardian Name:</div>
            <div class="col value">{{ $application->guardian_name ?? 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="col label">Relationship:</div>
            <div class="col value">{{ $application->guardian_relationship ?? 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="col label">Phone:</div>
            <div class="col value">{{ $application->guardian_phone ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Declaration</div>
        <p>I declare that the information provided in this application is true and correct to the best of my knowledge. I understand that any false information may lead to disqualification of my application or expulsion from the institution.</p>
        
        <div style="margin-top: 40px;">
            <div class="row">
                <div class="col" style="width: 50%;">
                    _________________________<br>
                    Signature
                </div>
                <div class="col" style="width: 50%;">
                    _________________________<br>
                    Date
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | KIBIHAS Online Application System
    </div>
</body>
</html>