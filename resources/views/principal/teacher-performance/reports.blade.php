@extends('layouts.portal')

@section('title', 'Reports Center - Teacher Performance')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports Center</h1>
            <p class="text-sm text-gray-500">Generate and export performance analytics reports</p>
        </div>
        <a href="{{ route('principal.teachers.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Generate Custom Report</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('principal.teachers.export') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Report Type -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="report" value="overview" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="block font-medium text-gray-900">Performance Overview</span>
                                    <span class="block text-gray-500">Summary of all teachers, departments, and key metrics.</span>
                                </div>
                            </label>
                            <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 focus-within:ring-2 focus-within:ring-indigo-500">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="report" value="teacher" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="block font-medium text-gray-900">Individual Teacher Scorecard</span>
                                    <span class="block text-gray-500">Detailed deep-dive into a specific teacher's performance.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach(\App\Models\AcademicYear::orderBy('name', 'desc')->get() as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester -->
                    <div>
                        <label for="semester_id" class="block text-sm font-medium text-gray-700">Semester</label>
                        <select name="semester_id" id="semester_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Semesters</option>
                            @foreach(\App\Models\Semester::all() as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department (Optional) -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department (Optional)</label>
                        <select name="department_id" id="department_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Departments</option>
                            @foreach(\App\Models\Department::orderBy('name')->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range (Optional) -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700">Date Range (Optional)</label>
                         <div class="mt-1 flex space-x-2">
                             <input type="date" name="start_date" class="block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" placeholder="Start Date">
                             <input type="date" name="end_date" class="block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" placeholder="End Date">
                         </div>
                    </div>

                    <!-- Teacher (Conditional) -->
                    <div x-data="{ show: false }" x-init="$watch('report', value => show = value === 'teacher')">
                        <label for="teacher" class="block text-sm font-medium text-gray-700">Teacher ID (Required for Scorecard)</label>
                        <input type="text" name="teacher" id="teacher" placeholder="Enter Teacher ID" class="mt-1 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Only required if 'Individual Teacher Scorecard' is selected.</p>
                    </div>

                    <!-- Format -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="radio" name="type" value="pdf" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700"><i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF Document</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="excel" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700"><i class="fas fa-file-excel text-green-500 mr-1"></i> Excel Spreadsheet</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-download mr-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
