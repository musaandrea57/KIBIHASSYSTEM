@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
        <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Students -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Students</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['students'] }}</p>
                </div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Teachers</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['teachers'] }}</p>
                </div>
            </div>
        </div>

        <!-- Departments -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-600">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Departments</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['departments'] }}</p>
                </div>
            </div>
        </div>

        <!-- Applications -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-600">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">New Applications</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['applications'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Academic Setup -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Academic Setup</h3>
            <div class="space-y-3">
                <a href="{{ route('academic-setup.programs.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Academic Programs</h4>
                    <p class="text-sm text-gray-500">Manage programs and courses</p>
                </a>
                <a href="{{ route('academic-setup.academic-years.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Academic Years</h4>
                    <p class="text-sm text-gray-500">Configure academic calendar</p>
                </a>
                <a href="{{ route('academic-setup.modules.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Modules</h4>
                    <p class="text-sm text-gray-500">Manage course modules</p>
                </a>
            </div>
        </div>

        <!-- Staff Management -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Staff Management</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.teachers.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Teachers</h4>
                    <p class="text-sm text-gray-500">Manage teaching staff</p>
                </a>
                <a href="{{ route('admin.departments.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Departments</h4>
                    <p class="text-sm text-gray-500">Manage departments</p>
                </a>
                <a href="{{ route('admin.assignments.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Module Assignments</h4>
                    <p class="text-sm text-gray-500">Assign teachers to modules</p>
                </a>
            </div>
        </div>

        <!-- System -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">System</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.integration.logs') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Integration Logs</h4>
                    <p class="text-sm text-gray-500">View NECTA/NACTE logs</p>
                </a>
                 <a href="{{ route('messages.index') }}" class="block p-4 border rounded hover:bg-gray-50 transition">
                    <h4 class="font-bold text-indigo-600">Messages</h4>
                    <p class="text-sm text-gray-500">Internal messaging system</p>
                </a>
            </div>
        </div>
    </div>

@endsection
