@extends('layouts.portal')

@section('content')
    <div class="mb-6"><div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Application Details: {{ $application->application_number }}
            </h2>
            <a href="{{ route('admin.admissions.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
        </div></div>

    @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column: Applicant Info -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- Personal Info -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Applicant Information</h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->first_name }} {{ $application->last_name }}</dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Email & Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->email }} | {{ $application->phone }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Program Choice</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->program->name }} ({{ $application->program->code }})</dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Biodata</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        DOB: {{ $application->biodata['dob'] ?? 'N/A' }}<br>
                                        Gender: {{ $application->biodata['gender'] ?? 'N/A' }}<br>
                                        Nationality: {{ $application->biodata['nationality'] ?? 'N/A' }}
                                    </dd>
                                </div>
                            </dl>

                    <!-- Education -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Education Background</h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">O-Level Index Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->education_background['index_number'] ?? 'N/A' }}</dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">School Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->education_background['school_name'] ?? 'N/A' }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Completion Year</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->education_background['completion_year'] ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Integration Hub -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-primary-100">
                        <div class="px-4 py-5 sm:px-6 bg-primary-50 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-bold text-primary-900">Integration Hub Verification</h3>
                            <span class="text-xs font-mono text-gray-500">API Gateway: Active</span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-4">
                                <button type="button" onclick="verifyNecta()" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                                    Verify NECTA Results
                                </button>
                                <button type="button" onclick="verifyNacte()" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                                    Check NACTVET Status
                                </button>
                            </div>

                            <div id="verification-results" class="hidden bg-gray-100 p-4 rounded text-sm font-mono border border-gray-300">
                                <!-- Results will be injected here via JS -->
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Documents & Actions -->
                <div class="md:col-span-1 space-y-6">
                    
                    <!-- Photo -->
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        @if(isset($application->documents['passport_photo']))
                            <img src="{{ Storage::url($application->documents['passport_photo']) }}" alt="Passport Photo" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-gray-200 mb-4">
                        @else
                            <div class="w-32 h-32 mx-auto rounded-full bg-gray-200 flex items-center justify-center mb-4">
                                <span class="text-gray-400">No Photo</span>
                            </div>
                        @endif
                        <h4 class="font-bold text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</h4>
                        <p class="text-sm text-gray-500">{{ $application->application_number }}</p>
                    </div>

                    <!-- Documents -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="font-bold text-gray-900 mb-4">Uploaded Documents</h4>
                        <ul class="space-y-3">
                            @foreach($application->documents as $key => $path)
                                @if($key != 'passport_photo')
                                    <li>
                                        <a href="{{ Storage::url($path) }}" target="_blank" class="flex items-center text-primary-600 hover:text-primary-800">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span class="capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="font-bold text-gray-900 mb-4">Admission Decision</h4>
                        
                        @if($application->status == 'submitted' || $application->status == 'under_review')
                            <div class="space-y-3">
                                <form action="{{ route('admin.admissions.approve', $application) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this application? This will generate a student record.');">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                        Approve & Admit
                                    </button>
                                </form>
                                <form action="{{ route('admin.admissions.reject', $application) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this application?');">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                                        Reject Application
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-4 rounded-md {{ $application->status == 'approved' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                                This application has been <strong>{{ $application->status }}</strong>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function verifyNecta() {
            const results = document.getElementById('verification-results');
            results.classList.remove('hidden');
            results.innerHTML = '<span class="text-yellow-600">Connecting to NECTA Gateway...</span>';
            
            fetch('{{ route('admin.integration.verify-necta') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    index_number: '{{ $application->education_background['index_number'] ?? "S1234/0001/2022" }}',
                    year: '{{ $application->education_background['completion_year'] ?? (date('Y') - 2) }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'VALID') {
                    results.innerHTML = `
                        <div class="text-green-600 font-bold mb-2">✓ Verified Successfully</div>
                        <div>Candidate: ${data.candidate_name}</div>
                        <div>Division: ${data.division} (Points: ${data.points})</div>
                        <div class="mt-1 font-bold text-xs">Subjects:</div>
                        <ul class="text-xs list-disc pl-4">
                            ${Object.entries(data.subjects).map(([sub, grade]) => `<li>${sub}: ${grade}</li>`).join('')}
                        </ul>
                    `;
                } else {
                    results.innerHTML = `<div class="text-red-600 font-bold">✗ Verification Failed</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                results.innerHTML = `<div class="text-red-600">System Error: Could not connect to Integration Hub.</div>`;
            });
        }

        function verifyNacte() {
            const results = document.getElementById('verification-results');
            results.classList.remove('hidden');
            results.innerHTML = '<span class="text-yellow-600">Connecting to NACTVET Gateway...</span>';
            
            fetch('{{ route('admin.integration.verify-nacte') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    avn: '{{ $application->education_background['nacte_reg_number'] ?? "NS1234" }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'VERIFIED') {
                    results.innerHTML = `
                        <div class="text-green-600 font-bold mb-2">✓ NACTVET Verified</div>
                        <div>Status: ${data.registration_status}</div>
                        <div>Award: ${data.award_level}</div>
                    `;
                } else {
                    results.innerHTML = `<div class="text-red-600 font-bold">✗ Verification Failed: ${data.status}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                results.innerHTML = `<div class="text-red-600">System Error: Could not connect to Integration Hub.</div>`;
            });
        }
    </script>

@endsection
