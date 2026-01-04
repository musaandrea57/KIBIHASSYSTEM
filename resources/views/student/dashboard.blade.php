@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Student Dashboard (SIMS)') }}</h2>
        <p class="text-gray-600">Welcome back, {{ $student->user->name }}</p>
    </div>

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

    <!-- Student Profile Summary -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-primary-900 text-white flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <div class="w-20 h-20 rounded-full bg-white p-1 overflow-hidden">
                        @if($student->profile_photo_path)
                            <img src="{{ Storage::url($student->profile_photo_path) }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <div class="w-full h-full rounded-full bg-primary-100 flex items-center justify-center text-primary-900 font-bold text-2xl">
                                {{ substr($student->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <!-- Photo Upload Overlay -->
                    <form action="{{ route('student.photo.update') }}" method="POST" enctype="multipart/form-data" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        @csrf
                        <label for="photo-upload" class="cursor-pointer text-white text-xs text-center p-1">
                            Change
                            <input type="file" name="photo" id="photo-upload" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
                <div>
                    <h3 class="text-2xl font-bold">{{ $student->user->name }}</h3>
                    <p class="text-primary-200">{{ $student->registration_number }} | {{ $student->program->code }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-primary-200">Current Session</div>
                <div class="font-bold text-lg">{{ $activeYear->name ?? 'N/A' }} - {{ $activeSemester->name ?? 'N/A' }}</div>
                <div class="text-sm bg-secondary text-primary-900 px-2 py-0.5 rounded inline-block mt-1 font-bold">NTA Level {{ $student->current_nta_level }}</div>
            </div>
        </div>
    </div>

    <!-- NHIF Status Widget -->
    @if(!$nhifMembership || $nhifMembership->status != 'active' || ($nhifMembership->expiry_date && $nhifMembership->expiry_date < now()->addDays(30)))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">NHIF Membership Status</h3>
                @if($nhifMembership)
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-{{ $nhifMembership->status_badge }}-100 text-{{ $nhifMembership->status_badge }}-800">
                        {{ strtoupper(str_replace('_', ' ', $nhifMembership->status)) }}
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        MISSING
                    </span>
                @endif
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        @if(!$nhifMembership)
                            <p class="text-red-700 font-medium">You have not submitted your NHIF details.</p>
                            <p class="text-sm text-gray-500 mt-1">Health insurance is mandatory for all students.</p>
                        @elseif($nhifMembership->status == 'expired')
                            <p class="text-red-700 font-medium">Your NHIF membership has expired.</p>
                            <p class="text-sm text-gray-500 mt-1">Please renew immediately to maintain coverage.</p>
                        @elseif($nhifMembership->status == 'pending_verification')
                             <p class="text-yellow-700 font-medium">Your NHIF details are pending verification.</p>
                        @elseif($nhifMembership->expiry_date < now()->addDays(30))
                             <p class="text-orange-700 font-medium">Your NHIF membership expires soon ({{ $nhifMembership->expiry_date->format('d M Y') }}).</p>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('student.nhif.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Manage NHIF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Fee Clearance Status Widget -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Fee Clearance Status</h3>
            @if($isCleared)
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    CLEARED
                </span>
            @else
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    NOT CLEARED
                </span>
            @endif
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    @if($isCleared)
                        <p class="text-green-700 font-medium">You are fully cleared for the current semester.</p>
                        <p class="text-sm text-gray-500 mt-1">You have access to all academic results and transcripts.</p>
                    @else
                        <p class="text-red-700 font-medium">You have outstanding fees for the current semester.</p>
                        <p class="text-sm text-gray-500 mt-1">Access to academic results and transcripts is restricted.</p>
                    @endif
                </div>
                <div>
                    @if(!$isCleared)
                        <a href="{{ route('student.finance.clearance_required') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            View Details
                        </a>
                    @else
                        <a href="{{ route('student.finance.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                            Finance Details
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Course Registration / List -->
        <div class="md:col-span-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-gray-800">My Modules</h3>
                </div>
                <div class="p-6">
                    @if($isRegistered)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Module Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($registrations as $reg)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $reg->module->code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $reg->module->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $reg->module->credits }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($reg->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        You have not registered for this semester's modules yet. Please select modules below and submit.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('student.courses.register') }}" method="POST">
                            @csrf
                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left"><input type="checkbox" checked disabled></th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Module Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($availableModules as $module)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <input type="checkbox" name="modules[]" value="{{ $module->id }}" checked class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $module->code }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $module->name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $module->credits }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">No modules available for registration. Contact Admin.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                            @if($availableModules->count() > 0)
                                <button type="submit" class="bg-primary-700 hover:bg-primary-800 text-white font-bold py-2 px-6 rounded transition">
                                    Confirm Registration
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="md:col-span-1 space-y-6">
            <!-- Academic Performance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Recent Results</h3>
                    
                    @if(isset($recentResults) && $recentResults->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentResults as $result)
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                    <div>
                                        <div class="text-sm font-bold text-gray-700">{{ $result->moduleOffering->module->code }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($result->moduleOffering->module->name, 20) }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold {{ $result->remark == 'Pass' ? 'text-green-600' : 'text-red-600' }}">{{ $result->grade }}</div>
                                    </div>
                                </div>
                            @endforeach
                            <a href="{{ route('student.results') }}" class="block text-center text-sm text-primary-600 hover:text-primary-800 font-bold mt-4">
                                View All Results &rarr;
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No results published recently.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Announcements</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 italic">No new announcements.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Financial Status</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Tuition Fee</span>
                        <span class="font-bold">1,500,000 TZS</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Paid</span>
                        <span class="font-bold text-green-600">0 TZS</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-800">Balance</span>
                        <span class="font-bold text-red-600">1,500,000 TZS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
