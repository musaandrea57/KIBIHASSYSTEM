@extends('layouts.portal')

@section('content')
    <!-- Main Container -->
    <div class="space-y-8" x-data="{ activeTab: 'academic' }">

        <!-- 1. Identity & Status Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
            <!-- Decorative Top Bar -->
            <div class="h-32 bg-slate-900 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800"></div>
                <!-- Abstract Academic Pattern -->
                <svg class="absolute right-0 top-0 h-full w-1/3 text-white/5" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
                </svg>
            </div>

            <div class="px-8 pb-8">
                <div class="relative flex flex-col md:flex-row items-end -mt-12 mb-4 gap-6">
                    <!-- Profile Photo -->
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full border-4 border-white bg-white shadow-md overflow-hidden z-10 relative">
                            @if($student->profile_photo_path)
                                <img src="{{ Storage::url($student->profile_photo_path) }}" class="w-full h-full object-cover" alt="Student Profile">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Photo Update Overlay -->
                            <form action="{{ route('student.photo.update') }}" method="POST" enctype="multipart/form-data" class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer">
                                @csrf
                                <label for="photo-upload" class="cursor-pointer text-white text-xs font-medium text-center p-2">
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Update
                                </label>
                                <input type="file" name="photo" id="photo-upload" class="hidden" onchange="this.form.submit()">
                            </form>
                        </div>
                    </div>

                    <!-- Identity Details -->
                    <div class="flex-1 pb-1">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $student->user->name }}</h1>
                                <div class="flex items-center gap-3 mt-1 text-gray-600 text-sm font-medium">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                        {{ $student->registration_number }}
                                    </span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span>{{ $student->program->code }}</span>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex items-center gap-3">
                                <div class="px-4 py-1.5 rounded-full text-sm font-bold border flex items-center gap-2 {{ $isRegistered ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    <span class="w-2 h-2 rounded-full {{ $isRegistered ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                    {{ $isRegistered ? 'Active Student' : 'Registration Pending' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Academic Snapshot Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 border-t border-gray-100 pt-6">
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors group">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 group-hover:text-blue-700">Programme</div>
                        <div class="font-bold text-gray-900 text-lg leading-tight truncate" title="{{ $student->program->name }}">{{ $student->program->name }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors group">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 group-hover:text-blue-700">Academic Year</div>
                        <div class="font-bold text-gray-900 text-lg">{{ $activeYear->name ?? 'N/A' }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors group">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 group-hover:text-blue-700">Semester</div>
                        <div class="font-bold text-gray-900 text-lg">{{ $activeSemester->name ?? 'N/A' }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition-colors group">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 group-hover:text-blue-700">NTA Level</div>
                        <div class="font-bold text-gray-900 text-lg">Level {{ $student->current_nta_level }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        @if ($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 mb-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">There were some problems with your input:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- 3. Information Architecture (Tabs) -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden min-h-[500px]">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 bg-gray-50/50">
                <nav class="flex overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'academic'" 
                        :class="{ 'border-blue-600 text-blue-700 bg-white': activeTab === 'academic', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'academic' }"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors duration-150 ease-in-out focus:outline-none">
                        Academic Information
                    </button>
                    <button @click="activeTab = 'profile'"
                        :class="{ 'border-blue-600 text-blue-700 bg-white': activeTab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'profile' }"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors duration-150 ease-in-out focus:outline-none">
                        Profile & Bio
                    </button>
                    <button @click="activeTab = 'finance'"
                        :class="{ 'border-blue-600 text-blue-700 bg-white': activeTab === 'finance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'finance' }"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors duration-150 ease-in-out focus:outline-none">
                        Financial Status
                    </button>
                    <button @click="activeTab = 'health'"
                        :class="{ 'border-blue-600 text-blue-700 bg-white': activeTab === 'health', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'health' }"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-colors duration-150 ease-in-out focus:outline-none">
                        Health & Emergency
                    </button>
                </nav>
            </div>

            <!-- Tab Content Area -->
            <div class="p-8 bg-white">
                
                <!-- Tab 1: Academic Information -->
                <div x-show="activeTab === 'academic'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Registration Status -->
                            <div class="bg-white">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    Module Registration
                                </h3>

                                @if($isRegistered)
                                    <div class="overflow-hidden rounded-lg border border-gray-200">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Code</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Name</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($registrations as $reg)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reg->module->code }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reg->module->name }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reg->module->credits }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                {{ ucfirst($reg->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-6 mb-6">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-amber-800">Registration Pending</h3>
                                                <p class="mt-1 text-sm text-amber-700">You have not registered for modules this semester. Please select your modules below to continue.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('student.courses.register') }}" method="POST" class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                        @csrf
                                        <div class="p-6">
                                            <div class="mb-4">
                                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Available Modules</h4>
                                            </div>
                                            <table class="min-w-full divide-y divide-gray-200 mb-6">
                                                <thead>
                                                    <tr>
                                                        <th class="px-4 py-2 text-left w-10"><input type="checkbox" checked disabled class="rounded border-gray-300 text-blue-600"></th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Module Name</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @forelse($availableModules as $module)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <input type="checkbox" name="modules[]" value="{{ $module->id }}" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                            </td>
                                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $module->code }}</td>
                                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $module->name }}</td>
                                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $module->credits }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">No modules available for registration. Please contact the academic office.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            
                                            @if($availableModules->count() > 0)
                                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                        Confirm & Register Modules
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Right Sidebar -->
                        <div class="space-y-6">
                             <!-- Results Widget -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Recent Performance</h3>
                                @if(isset($recentResults) && $recentResults->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($recentResults as $result)
                                            <div class="flex items-center justify-between group">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 group-hover:text-blue-700 transition-colors">{{ $result->moduleOffering->module->code }}</div>
                                                    <div class="text-xs text-gray-500 truncate w-32">{{ $result->moduleOffering->module->name }}</div>
                                                </div>
                                                <div class="text-sm font-bold {{ $result->remark == 'Pass' ? 'text-green-700 bg-green-50 px-2 py-0.5 rounded' : 'text-red-700 bg-red-50 px-2 py-0.5 rounded' }}">
                                                    {{ $result->grade }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('student.results.index') }}" class="block mt-6 text-center text-sm font-medium text-blue-700 hover:text-blue-800 hover:underline">View Full Transcript</a>
                                @else
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-500 italic">No results published yet.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Advisor Card -->
                            <div class="bg-slate-50 rounded-lg border border-slate-200 p-6">
                                <h3 class="font-bold text-slate-800 mb-2">Academic Advisor</h3>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">Department Head</div>
                                        <div class="text-xs text-gray-500">Contact Dept Office</div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">For academic guidance, please schedule an appointment during office hours.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Profile & Bio -->
                <div x-show="activeTab === 'profile'" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Personal Details -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="font-bold text-gray-800">Personal Details</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Full Name</div>
                                    <div class="col-span-2 text-sm font-semibold text-gray-900">{{ $student->user->name }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Email</div>
                                    <div class="col-span-2 text-sm text-gray-900">{{ $student->user->email }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Gender</div>
                                    <div class="col-span-2 text-sm text-gray-900">{{ ucfirst($student->gender) }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Phone</div>
                                    <div class="col-span-2 text-sm text-gray-900">{{ $student->phone ?? 'Not Provided' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Registration -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="font-bold text-gray-800">Academic Registration</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Reg. Number</div>
                                    <div class="col-span-2 text-sm font-semibold text-gray-900 font-mono">{{ $student->registration_number }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Program</div>
                                    <div class="col-span-2 text-sm text-gray-900">{{ $student->program->name }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Admission Year</div>
                                    <div class="col-span-2 text-sm text-gray-900">{{ $student->admission_year }}</div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-b border-gray-50 pb-3 last:border-0">
                                    <div class="text-sm font-medium text-gray-500">Current Status</div>
                                    <div class="col-span-2 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Finance -->
                <div x-show="activeTab === 'finance'" x-cloak>
                    <div class="max-w-3xl mx-auto space-y-6">
                        <!-- Clearance Status -->
                        <div class="bg-white rounded-xl border {{ $isCleared ? 'border-green-200' : 'border-red-200' }} shadow-sm overflow-hidden">
                            <div class="px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-6">
                                <div class="flex items-start gap-4">
                                    <div class="p-3 rounded-full {{ $isCleared ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        @if($isCleared)
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $isCleared ? 'Fully Cleared' : 'Action Required' }}</h3>
                                        <p class="text-gray-600 mt-1 max-w-md">
                                            @if($isCleared)
                                                You have met all financial obligations for the current semester. You have full access to academic services.
                                            @else
                                                You have outstanding fees. Please settle your balance to access examination results and transcripts.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    @if(!$isCleared)
                                        <a href="{{ route('student.finance.clearance_required') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm">
                                            View Outstanding Balance
                                        </a>
                                    @else
                                        <a href="{{ route('student.finance.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            Payment History
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Invoiced</h4>
                                <div class="text-2xl font-bold text-gray-900">TZS 1,500,000</div>
                                <p class="text-xs text-gray-400 mt-1">Current Academic Year</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Outstanding Balance</h4>
                                <div class="text-2xl font-bold {{ $isCleared ? 'text-gray-900' : 'text-red-600' }}">TZS {{ $isCleared ? '0' : '1,500,000' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Health & Emergency -->
                <div x-show="activeTab === 'health'" x-cloak>
                    <div class="max-w-3xl mx-auto">
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800">Health Insurance (NHIF)</h3>
                                @if($nhifMembership)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $nhifMembership->status_badge }}-100 text-{{ $nhifMembership->status_badge }}-800">
                                        {{ strtoupper(str_replace('_', ' ', $nhifMembership->status)) }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">MISSING</span>
                                @endif
                            </div>
                            <div class="p-8 text-center">
                                @if(!$nhifMembership)
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">NHIF Details Missing</h4>
                                    <p class="text-gray-500 max-w-md mx-auto mb-6">Health insurance information is mandatory for all students. Please update your records to avoid service interruption.</p>
                                    <a href="{{ route('student.nhif.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-900 hover:bg-blue-800 transition-colors">
                                        Update NHIF Information
                                    </a>
                                @else
                                    <div class="grid grid-cols-2 gap-6 text-left max-w-lg mx-auto">
                                        <div>
                                            <div class="text-xs text-gray-500 uppercase tracking-wide">Card Number</div>
                                            <div class="font-mono font-bold text-lg text-gray-900">{{ $nhifMembership->card_number }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 uppercase tracking-wide">Expiry Date</div>
                                            <div class="font-bold text-lg {{ $nhifMembership->expiry_date < now() ? 'text-red-600' : 'text-gray-900' }}">{{ $nhifMembership->expiry_date->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-8 pt-6 border-t border-gray-100">
                                        <a href="{{ route('student.nhif.index') }}" class="text-blue-700 font-medium hover:underline">Manage Membership &rarr;</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                            <h4 class="font-bold text-blue-900 mb-2">Emergency Contacts</h4>
                            <p class="text-sm text-blue-800 mb-4">In case of emergency, please contact the University Health Center or Campus Security.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded shadow-sm border border-blue-100">
                                    <div class="font-bold text-gray-900">Health Center</div>
                                    <div class="text-sm text-gray-600">+255 123 456 789</div>
                                </div>
                                <div class="bg-white p-3 rounded shadow-sm border border-blue-100">
                                    <div class="font-bold text-gray-900">Campus Security</div>
                                    <div class="text-sm text-gray-600">+255 987 654 321</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4. Action Zone -->
        <div class="flex justify-end gap-4 py-4 border-t border-gray-200">
            <a href="{{ route('student.profile.download') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-md font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Download Profile
            </a>
            <a href="{{ route('profile.edit') }}" class="px-6 py-2.5 bg-blue-900 border border-transparent rounded-md font-medium text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Update Profile
            </a>
        </div>

    </div>
@endsection
