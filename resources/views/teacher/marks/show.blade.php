@extends('layouts.portal')

@section('content')

@php
    $initialMarks = [];
    $studentIds = [];
    foreach($registrations as $reg) {
        $result = $results->get($reg->student_id); 
        $assessments = $result ? $result->continuousAssessments : collect();
        
        $t1Id = $assessmentTypeIds['TEST1'] ?? 0;
        $t2Id = $assessmentTypeIds['TEST2'] ?? 0;
        $a1Id = $assessmentTypeIds['ASSIGN1'] ?? 0;
        $a2Id = $assessmentTypeIds['ASSIGN2'] ?? 0;
        
        $initialMarks[$reg->student_id] = [
            'test1' => $assessments->where('assessment_type_id', $t1Id)->first()?->mark,
            'test2' => $assessments->where('assessment_type_id', $t2Id)->first()?->mark,
            'assign1' => $assessments->where('assessment_type_id', $a1Id)->first()?->mark,
            'assign2' => $assessments->where('assessment_type_id', $a2Id)->first()?->mark,
            'se' => $result ? ($result->se_mark ? $result->se_mark / 0.6 : null) : null,
        ];
        $studentIds[] = $reg->student_id;
    }
@endphp

<div class="space-y-6" x-data="marksEntry(@js($initialMarks), @js($studentIds))">

    <!-- Breadcrumb & Header -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li><a href="{{ route('teacher.marks.index') }}" class="text-gray-500 hover:text-gray-700">Marks Entry Center</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li class="text-gray-900 font-medium">{{ $offering->module->code }}</li>
        </ol>
    </nav>

    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $offering->module->name }} ({{ $offering->module->code }})</h1>
            <div class="mt-1 flex items-center gap-4 text-sm text-gray-500">
                <span>NTA Level {{ $offering->nta_level }}</span>
                <span>&bull;</span>
                <span>{{ $offering->semester->name }}</span>
                <span>&bull;</span>
                <span>{{ $offering->academicYear->name }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <!-- Toggle Release Button -->
            <form action="{{ route('teacher.marks.toggle-release', $offering->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 {{ $offering->coursework_released ? 'bg-amber-600 hover:bg-amber-700' : 'bg-gray-600 hover:bg-gray-700' }} border border-transparent rounded-md text-sm font-medium text-white" title="{{ $offering->coursework_released ? 'Click to Hide Coursework from Students' : 'Click to Release Coursework to Students' }}">
                    {{ $offering->coursework_released ? 'Hide Coursework' : 'Release Coursework' }}
                </button>
            </form>

            <button @click="showImportModal = true" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Import Excel
            </button>
            <button @click="showSubmitModal = true" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                Preview & Submit
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mt-4 relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
        <input type="text" x-model="searchQuery" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search students by name or registration number...">
    </div>

    <!-- Alert for Semester II -->
    @if($isSemesterTwo)
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Semester II results are issued via official NACTVET uploads. Manual entry is disabled.
                </p>
            </div>
        </div>
    </div>
    @endif
    
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('import_failures'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Import Validation Errors</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>The following errors were found in your Excel file. Please correct them and try again.</p>
                </div>
            </div>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-red-200">
                <thead class="bg-red-100">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-red-700 uppercase">Row</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-red-700 uppercase">Column</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-red-700 uppercase">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-200 bg-red-50">
                    @foreach(session('import_failures') as $failure)
                    <tr>
                        <td class="px-3 py-2 text-sm text-red-900">{{ $failure->row() }}</td>
                        <td class="px-3 py-2 text-sm text-red-900">{{ $failure->attribute() }}</td>
                        <td class="px-3 py-2 text-sm text-red-900">
                            <ul class="list-disc list-inside">
                                @foreach($failure->errors() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'ca'" :class="{'border-blue-500 text-blue-600': activeTab === 'ca', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'ca'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Continuous Assessment (40%)
            </button>
            <button @click="activeTab = 'se'" :class="{'border-blue-500 text-blue-600': activeTab === 'se', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'se'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Semester Exam (60%)
            </button>
            <button @click="activeTab = 'preview'" :class="{'border-blue-500 text-blue-600': activeTab === 'preview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'preview'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Preview Final
            </button>
            <button @click="activeTab = 'history'" :class="{'border-blue-500 text-blue-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Submission & History
            </button>
        </nav>
    </div>

    <!-- Modals -->
    <!-- Import Modal -->
    <div x-show="showImportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showImportModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('teacher.marks.import', $offering->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Marks from Excel</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Upload an Excel file to import marks. Please ensure you use the template provided to avoid errors.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('teacher.marks.export', $offering->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download Template</a>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700">Select File</label>
                                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Import
                        </button>
                        <button type="button" @click="showImportModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Submit Modal -->
    <div x-show="showSubmitModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showSubmitModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('teacher.marks.submit', $offering->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Submit Marks for Approval</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to submit these marks? Once submitted, you will not be able to make further changes until an administrator approves or rejects them.
                                    </p>
                                    <ul class="mt-4 list-disc list-inside text-sm text-gray-500">
                                        <li>Ensure all CA marks are entered correctly.</li>
                                        <li>Ensure all SE marks are entered correctly.</li>
                                        <li>Check for any missing values.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Submission
                        </button>
                        <button type="button" @click="showSubmitModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('teacher.marks.store', $offering->id) }}" method="POST" id="marksForm">

        @csrf
        
        <!-- CA Tab -->
        <div x-show="activeTab === 'ca'" class="bg-white shadow rounded-lg overflow-hidden flex flex-col h-[70vh]">
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 relative">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 top-0 bg-gray-50 z-30 shadow-sm">Student Info</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">
                                Test 1 (100) <button type="button" @click="fillDown('test1')" class="text-blue-600 hover:text-blue-800" title="Fill Down">&darr;</button>
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">
                                Test 2 (100) <button type="button" @click="fillDown('test2')" class="text-blue-600 hover:text-blue-800" title="Fill Down">&darr;</button>
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100 sticky top-0 z-20 shadow-sm">Avg Test</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">
                                Assign 1 (100) <button type="button" @click="fillDown('assign1')" class="text-blue-600 hover:text-blue-800" title="Fill Down">&darr;</button>
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">
                                Assign 2 (100) <button type="button" @click="fillDown('assign2')" class="text-blue-600 hover:text-blue-800" title="Fill Down">&darr;</button>
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100 sticky top-0 z-20 shadow-sm">Avg Assign</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50 sticky top-0 z-20 shadow-sm">CA (40%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($registrations as $reg)
                        <tr x-show="matchesSearch('{{ strtolower($reg->student->first_name . ' ' . $reg->student->last_name . ' ' . $reg->student->registration_number) }}')">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                <div class="text-sm font-medium text-gray-900">{{ $reg->student->first_name }} {{ $reg->student->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reg->student->registration_number }}</div>
                            </td>
                            <!-- Inputs with x-model and keyboard nav -->
                            <td class="px-2 py-4">
                                <input type="number" step="0.1" min="0" max="100" 
                                    id="input-{{ $reg->student_id }}-test1"
                                    name="marks[{{ $reg->student_id }}][test1]" 
                                    x-model.number="marks['{{ $reg->student_id }}'].test1" 
                                    @keydown.arrow-up.prevent="moveFocus('up', {{ $reg->student_id }}, 'test1')"
                                    @keydown.arrow-down.prevent="moveFocus('down', {{ $reg->student_id }}, 'test1')"
                                    @keydown.enter.prevent="moveFocus('down', {{ $reg->student_id }}, 'test1')"
                                    @keydown.arrow-right.prevent="focusNext({{ $reg->student_id }}, 'test1', 'test2')"
                                    {{ $isSemesterTwo ? 'disabled' : '' }} 
                                    class="w-20 text-center text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-4">
                                <input type="number" step="0.1" min="0" max="100" 
                                    id="input-{{ $reg->student_id }}-test2"
                                    name="marks[{{ $reg->student_id }}][test2]" 
                                    x-model.number="marks['{{ $reg->student_id }}'].test2" 
                                    @keydown.arrow-up.prevent="moveFocus('up', {{ $reg->student_id }}, 'test2')"
                                    @keydown.arrow-down.prevent="moveFocus('down', {{ $reg->student_id }}, 'test2')"
                                    @keydown.enter.prevent="moveFocus('down', {{ $reg->student_id }}, 'test2')"
                                    @keydown.arrow-left.prevent="focusNext({{ $reg->student_id }}, 'test2', 'test1')"
                                    @keydown.arrow-right.prevent="focusNext({{ $reg->student_id }}, 'test2', 'assign1')"
                                    {{ $isSemesterTwo ? 'disabled' : '' }} 
                                    class="w-20 text-center text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-4 bg-gray-50 text-center text-sm font-medium text-gray-700" x-text="calcAvg('{{ $reg->student_id }}', 'test')">-</td> 
                            
                            <td class="px-2 py-4">
                                <input type="number" step="0.1" min="0" max="100" 
                                    id="input-{{ $reg->student_id }}-assign1"
                                    name="marks[{{ $reg->student_id }}][assign1]" 
                                    x-model.number="marks['{{ $reg->student_id }}'].assign1" 
                                    @keydown.arrow-up.prevent="moveFocus('up', {{ $reg->student_id }}, 'assign1')"
                                    @keydown.arrow-down.prevent="moveFocus('down', {{ $reg->student_id }}, 'assign1')"
                                    @keydown.enter.prevent="moveFocus('down', {{ $reg->student_id }}, 'assign1')"
                                    @keydown.arrow-left.prevent="focusNext({{ $reg->student_id }}, 'assign1', 'test2')"
                                    @keydown.arrow-right.prevent="focusNext({{ $reg->student_id }}, 'assign1', 'assign2')"
                                    {{ $isSemesterTwo ? 'disabled' : '' }} 
                                    class="w-20 text-center text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-4">
                                <input type="number" step="0.1" min="0" max="100" 
                                    id="input-{{ $reg->student_id }}-assign2"
                                    name="marks[{{ $reg->student_id }}][assign2]" 
                                    x-model.number="marks['{{ $reg->student_id }}'].assign2" 
                                    @keydown.arrow-up.prevent="moveFocus('up', {{ $reg->student_id }}, 'assign2')"
                                    @keydown.arrow-down.prevent="moveFocus('down', {{ $reg->student_id }}, 'assign2')"
                                    @keydown.enter.prevent="moveFocus('down', {{ $reg->student_id }}, 'assign2')"
                                    @keydown.arrow-left.prevent="focusNext({{ $reg->student_id }}, 'assign2', 'assign1')"
                                    {{ $isSemesterTwo ? 'disabled' : '' }} 
                                    class="w-20 text-center text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-4 bg-gray-50 text-center text-sm font-medium text-gray-700" x-text="calcAvg('{{ $reg->student_id }}', 'assign')">-</td>
                            
                            <td class="px-2 py-4 bg-blue-50 text-center text-sm font-bold text-blue-800" x-text="calcCA('{{ $reg->student_id }}')">-</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!$isSemesterTwo)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end shrink-0">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save Changes</button>
            </div>
            @endif
        </div>

        <!-- SE Tab -->
        <div x-show="activeTab === 'se'" class="bg-white shadow rounded-lg overflow-hidden flex flex-col h-[70vh]" style="display: none;">
            <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 relative">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 top-0 bg-gray-50 z-30 shadow-sm">Student Info</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">
                                Written Exam (100) <button type="button" @click="fillDown('se')" class="text-blue-600 hover:text-blue-800" title="Fill Down">&darr;</button>
                            </th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50 sticky top-0 z-20 shadow-sm">SE (60%)</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                         @foreach($registrations as $reg)
                        <tr x-show="matchesSearch('{{ strtolower($reg->student->first_name . ' ' . $reg->student->last_name . ' ' . $reg->student->registration_number) }}')">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                <div class="text-sm font-medium text-gray-900">{{ $reg->student->first_name }} {{ $reg->student->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reg->student->registration_number }}</div>
                            </td>
                            <td class="px-2 py-4 text-center">
                                <input type="number" step="0.1" min="0" max="100" 
                                    id="input-{{ $reg->student_id }}-se"
                                    name="marks[{{ $reg->student_id }}][se]" 
                                    x-model.number="marks['{{ $reg->student_id }}'].se" 
                                    @keydown.arrow-up.prevent="moveFocus('up', {{ $reg->student_id }}, 'se')"
                                    @keydown.arrow-down.prevent="moveFocus('down', {{ $reg->student_id }}, 'se')"
                                    @keydown.enter.prevent="moveFocus('down', {{ $reg->student_id }}, 'se')"
                                    {{ $isSemesterTwo ? 'disabled' : '' }} 
                                    class="w-24 text-center text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </td>
                            <td class="px-2 py-4 bg-blue-50 text-center text-sm font-bold text-blue-800" x-text="marks['{{ $reg->student_id }}'].se ? (marks['{{ $reg->student_id }}'].se * 0.6).toFixed(1) : '-'">-</td>
                            <td class="px-2 py-4 text-center">
                                <select class="text-xs border-gray-300 rounded-md" {{ $isSemesterTwo ? 'disabled' : '' }}>
                                    <option>Present</option>
                                    <option>Absent</option>
                                    <option>Withheld</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!$isSemesterTwo)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end shrink-0">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">Save Changes</button>
            </div>
            @endif
        </div>

        <!-- Preview Final Tab -->
        <div x-show="activeTab === 'preview'" class="bg-white shadow rounded-lg overflow-hidden flex flex-col h-[70vh]" style="display: none;">
             <div class="overflow-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 relative">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 top-0 bg-gray-50 z-30 shadow-sm">Student Info</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">CW (40%)</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">SE (60%)</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100 sticky top-0 z-20 shadow-sm">Total</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">Grade</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider sticky top-0 bg-gray-50 z-20 shadow-sm">Remark</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($registrations as $reg)
                        @php 
                             $result = $results->get($reg->student_id); 
                        @endphp
                        <tr x-show="matchesSearch('{{ strtolower($reg->student->first_name . ' ' . $reg->student->last_name . ' ' . $reg->student->registration_number) }}')">
                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                <div class="text-sm font-medium text-gray-900">{{ $reg->student->first_name }} {{ $reg->student->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reg->student->registration_number }}</div>
                            </td>
                            <td class="px-3 py-4 text-center text-sm text-gray-500" x-text="calcCA('{{ $reg->student_id }}')">-</td>
                            <td class="px-3 py-4 text-center text-sm text-gray-500" x-text="marks['{{ $reg->student_id }}'].se ? (marks['{{ $reg->student_id }}'].se * 0.6).toFixed(1) : '-'">-</td>
                            <td class="px-3 py-4 text-center text-sm font-bold bg-gray-50">
                                <span x-text="(parseFloat(calcCA('{{ $reg->student_id }}')) + (marks['{{ $reg->student_id }}'].se ? parseFloat(marks['{{ $reg->student_id }}'].se * 0.6) : 0)).toFixed(1)"></span>
                            </td>
                            <td class="px-3 py-4 text-center text-sm font-bold">
                                - <!-- Grade calc in JS is complex, keep server side for now or basic -->
                            </td>
                            <td class="px-3 py-4 text-center text-sm text-gray-500">{{ $result->remark ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History Tab -->
        <div x-show="activeTab === 'history'" class="bg-white shadow rounded-lg p-6" style="display: none;">    
             <h3 class="text-lg font-medium text-gray-900 mb-4">Submission Status</h3>

             <div class="bg-gray-50 p-4 rounded-md mb-6">
                 <div class="flex items-center justify-between">
                     <div>
                         <p class="text-sm font-medium text-gray-500">Current Status</p>
                         <p class="text-lg font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $offering->status ?? 'draft') }}</p>
                     </div>
                     @if(($offering->status ?? 'draft') == 'pending_approval')
                     <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        Pending Admin Review
                     </span>
                     @elseif(($offering->status ?? 'draft') == 'published')
                     <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Published
                     </span>
                     @else
                     <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Draft
                     </span>
                     @endif
                 </div>

                 @if(($offering->status ?? 'draft') == 'draft' || ($offering->status ?? 'draft') == 'returned') 
                 <div class="mt-4">
                     <button type="button" @click="showSubmitModal = true" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                         Submit for Approval
                     </button>
                 </div>
                 @endif
             </div>

             <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Trail</h3>
             <div class="flow-root">
                 <ul class="-mb-8">
                    <li>
                        <div class="relative pb-8">     
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Module Offering Initialized <span class="font-medium text-gray-900">System</span></p>
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                        <time datetime="{{ $offering->created_at }}">{{ $offering->created_at->format('M d, Y') }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                 </ul>
             </div>
        </div>
    </form>
</div>

<script>
    function marksEntry(initialData, sortedIds) {
        return {
            activeTab: 'ca',
            searchQuery: '',
            showImportModal: false,
            showSubmitModal: false,
            marks: initialData,
            studentIds: sortedIds,
            
            matchesSearch(text) {
                if (this.searchQuery === '') return true;
                return text.includes(this.searchQuery.toLowerCase());
            },

            moveFocus(direction, currentId, currentField) {
                // Get visible student IDs (filtered by search)
                // This is a bit tricky with Alpine inside a loop, but we can iterate the DOM or just use the full list
                // Using full list for simplicity, but jumping over hidden ones would be better.
                // For now, just simple navigation in the full list.
                
                const currentIndex = this.studentIds.indexOf(currentId);
                let nextIndex = currentIndex;
                
                if (direction === 'down') nextIndex++;
                if (direction === 'up') nextIndex--;
                
                if (nextIndex >= 0 && nextIndex < this.studentIds.length) {
                    const nextId = this.studentIds[nextIndex];
                    const el = document.getElementById(`input-${nextId}-${currentField}`);
                    if (el) {
                         // Check if visible (if we implement skipping hidden rows later)
                         el.focus();
                         // el.select(); // Optional: select content
                    }
                }
            },
            
            focusNext(id, currentField, nextField) {
                 const el = document.getElementById(`input-${id}-${nextField}`);
                 if (el) el.focus();
            },

            calcAvg(id, type) {
                let v1 = this.marks[id][type+'1'];
                let v2 = this.marks[id][type+'2'];
                
                // Treat empty string or null as null
                v1 = (v1 === '' || v1 === null) ? null : parseFloat(v1);
                v2 = (v2 === '' || v2 === null) ? null : parseFloat(v2);
                
                if (v1 === null && v2 === null) return '-';
                if (v1 === null) return v2;
                if (v2 === null) return v1;
                
                return ((v1 + v2) / 2).toFixed(1);
            },
            
            calcCA(id) {
                let t1 = this.marks[id].test1;
                let t2 = this.marks[id].test2;
                let a1 = this.marks[id].assign1;
                let a2 = this.marks[id].assign2;

                // Treat empty string or null as null
                t1 = (t1 === '' || t1 === null) ? null : parseFloat(t1);
                t2 = (t2 === '' || t2 === null) ? null : parseFloat(t2);
                a1 = (a1 === '' || a1 === null) ? null : parseFloat(a1);
                a2 = (a2 === '' || a2 === null) ? null : parseFloat(a2);
                
                if (t1 === null && t2 === null && a1 === null && a2 === null) return 0;
                
                // Calculate Average Tests
                let tAvg = 0;
                if (t1 !== null && t2 !== null) tAvg = (t1 + t2) / 2;
                else if (t1 !== null) tAvg = t1;
                else if (t2 !== null) tAvg = t2;
                
                // Calculate Average Assignments
                let aAvg = 0;
                if (a1 !== null && a2 !== null) aAvg = (a1 + a2) / 2;
                else if (a1 !== null) aAvg = a1;
                else if (a2 !== null) aAvg = a2;
                
                let ca = (tAvg * 0.2) + (aAvg * 0.2);
                return ca.toFixed(1);
            },
            
            fillDown(field) {
                if (this.studentIds.length === 0) return;
                
                const firstId = this.studentIds[0];
                const val = this.marks[firstId][field];
                
                this.studentIds.forEach(id => {
                    this.marks[id][field] = val;
                });
            }
        }
    }
</script>
@endsection
