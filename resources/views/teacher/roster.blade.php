@extends('layouts.portal')

@section('content')
    <div class="mb-6"><div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Class Roster') }}: {{ $assignment->moduleOffering->module->code }} - {{ $assignment->moduleOffering->module->name }}
            </h2>
            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Back to Dashboard
            </a>
        </div></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Program: <span class="font-semibold">{{ $assignment->moduleOffering->module->program->name ?? 'N/A' }}</span></p>
                                <p class="text-sm text-gray-600">Level: <span class="font-semibold">NTA {{ $assignment->moduleOffering->nta_level }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Semester: <span class="font-semibold">{{ $assignment->moduleOffering->semester->name }}</span></p>
                                <p class="text-sm text-gray-600">Academic Year: <span class="font-semibold">{{ $assignment->moduleOffering->academicYear->name }}</span></p>
                    </div>

                    @if($students->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            No approved student registrations found for this module.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $index => $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->registration_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->gender ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Registered
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-sm text-gray-500">
                            Total Students: {{ $students->count() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection