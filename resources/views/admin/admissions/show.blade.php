@extends('layouts.portal')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Application: {{ $application->application_number }}
            </h2>
            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $application->status == 'approved' ? 'bg-green-100 text-green-800' : 
                           ($application->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                           ($application->status == 'correction_required' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Submitted on {{ $application->submitted_at ? $application->submitted_at->format('M d, Y') : 'N/A' }}
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.admissions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 1. Personal Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Applicant's identity and basic details.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Gender / DOB</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ ucfirst($application->gender) }} / {{ $application->dob ? $application->dob->format('d M Y') : 'N/A' }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nationality / Marital Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->nationality }} / {{ ucfirst($application->marital_status) }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">NIN / Passport</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                NIN: {{ $application->nin ?? 'N/A' }}<br>
                                Passport: {{ $application->passport_number ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- 2. Contact & Address -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Contact & Address</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Phone & Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->phone }} <br> {{ $application->email }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Current Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->current_address }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Permanent Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->permanent_address }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Region: {{ $application->region }} <br>
                                Country: {{ $application->country }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- 3. Education -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Education Background</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        @if($application->education_background)
                            @foreach($application->education_background as $key => $value)
                                @if(is_array($value))
                                    <!-- Skip complex arrays if not handled, or iterate -->
                                @else
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $value }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="py-4 px-6 text-sm text-gray-500">No education details provided.</div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- 4. Health & Emergency -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Health & Emergency</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Disability Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $application->has_disability ? 'Yes - ' . $application->disability_details : 'None' }}
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Medical Conditions</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->medical_conditions ?? 'None' }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">NHIF Card</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $application->nhif_card_number ?? 'N/A' }}</dd>
                        </div>
                        @if($application->emergency_contact)
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Emergency Contact</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    Name: {{ $application->emergency_contact['name'] ?? '' }}<br>
                                    Relation: {{ $application->emergency_contact['relationship'] ?? '' }}<br>
                                    Phone: {{ $application->emergency_contact['phone'] ?? '' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="space-y-6">
            
            <!-- Program Choice -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-indigo-50 border-b border-indigo-100">
                    <h3 class="text-lg leading-6 font-medium text-indigo-900">Program Selection</h3>
                </div>
                <div class="px-4 py-5 sm:px-6">
                    <p class="text-sm text-gray-500 mb-1">Applied Program</p>
                    <p class="text-lg font-bold text-gray-900">{{ $application->program->name }}</p>
                    <p class="text-sm text-gray-500 mt-2">Code: {{ $application->program->code }}</p>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">Sponsorship</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($application->sponsorship) }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Documents</h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($application->uploadedDocuments as $doc)
                        <li class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center truncate">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-2 flex flex-col truncate">
                                    <span class="text-sm font-medium text-gray-900 truncate capitalize">{{ str_replace('_', ' ', $doc->type) }}</span>
                                    <span class="text-xs text-gray-500">{{ $doc->original_name }} ({{ $doc->size_kb }} KB)</span>
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                <a href="{{ Storage::url($doc->path) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500 text-sm">View</a>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 text-sm text-gray-500 text-center">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Action Panel -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Admission Decision</h3>
                </div>
                <div class="px-4 py-5 sm:px-6 space-y-4">
                    @if($application->status == 'submitted' || $application->status == 'under_review' || $application->status == 'correction_required')
                        
                        <!-- Approve -->
                        <form action="{{ route('admin.admissions.approve', $application) }}" method="POST" onsubmit="return confirm('Confirm Admission? This will generate a student ID.');">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Approve & Admit
                            </button>
                        </form>

                        <!-- Request Correction -->
                        <div x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                Request Correction
                            </button>
                            <div x-show="open" class="mt-4 p-4 bg-yellow-50 rounded-md">
                                <form action="{{ route('admin.admissions.request_correction', $application) }}" method="POST">
                                    @csrf
                                    <label for="admin_feedback" class="block text-sm font-medium text-gray-700">Feedback / Instructions</label>
                                    <textarea name="admin_feedback" id="admin_feedback" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md mt-1" required placeholder="Describe what needs to be fixed..."></textarea>
                                    <div class="mt-3 flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Send Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Reject -->
                        <form action="{{ route('admin.admissions.reject', $application) }}" method="POST" onsubmit="return confirm('Are you sure you want to REJECT this application?');">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reject Application
                            </button>
                        </form>

                    @else
                        <div class="rounded-md bg-gray-50 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-800">
                                        Status: {{ ucfirst($application->status) }}
                                    </h3>
                                    @if($application->status == 'approved')
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>Student record created.</p>
                                        </div>
                                    @endif
                                    @if($application->admin_feedback)
                                        <div class="mt-2 text-sm text-gray-700 border-t pt-2">
                                            <span class="font-bold">Last Feedback:</span> {{ $application->admin_feedback }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
