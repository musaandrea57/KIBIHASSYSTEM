@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Details') }}
        </h2></div>

    @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Profile Card -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        <div class="w-32 h-32 mx-auto mb-4">
                            @if($student->profile_photo_path)
                                <img src="{{ Storage::url($student->profile_photo_path) }}" class="w-full h-full rounded-full object-cover border-4 border-gray-200">
                            @else
                                <div class="w-full h-full rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-3xl font-bold border-4 border-gray-200">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h3>
                        <p class="text-gray-500">{{ $student->registration_number }}</p>
                        <p class="text-sm text-gray-400">{{ $student->user->email }}</p>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Academic Info</h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="block text-gray-500">Program</span>
                                <span class="block font-medium">{{ $student->program->name }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500">NTA Level</span>
                                <span class="block font-medium">{{ $student->current_nta_level }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500">Session</span>
                                <span class="block font-medium">{{ $student->currentAcademicYear->name ?? 'N/A' }} ({{ $student->currentSemester->name ?? 'N/A' }})</span>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- Guardians / Parents -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Parents / Guardians</h3>
                            <a href="{{ route('admin.students.guardian.create', $student) }}" class="bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold py-2 px-4 rounded">
                                Link Parent
                            </a>
                        </div>
                        <div class="p-6">
                            @if($student->guardians->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Relationship</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($student->guardians as $guardian)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $guardian->user->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $guardian->user->email }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $guardian->relationship }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 italic text-center py-4">No parents or guardians linked.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Other sections (Results, Finance, etc.) can be added here -->
                    
                </div>
            </div>
        </div>
    </div>

@endsection