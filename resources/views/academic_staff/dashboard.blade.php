@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Academic Staff Dashboard') }}</h2>
        <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Programs</div>
            <div class="text-2xl font-bold">{{ $stats['programs'] }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Modules</div>
            <div class="text-2xl font-bold">{{ $stats['modules'] }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Integration</div>
            <div class="mt-2">
                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">NACTVET Portal &rarr;</a>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Academic Management</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('academic-setup.programs.index') }}" class="block p-4 border rounded hover:bg-gray-50">
                <h4 class="font-bold">Manage Programs</h4>
                <p class="text-sm text-gray-500">Edit curriculum and structures</p>
            </a>
            <a href="{{ route('academic-setup.module-offerings.index') }}" class="block p-4 border rounded hover:bg-gray-50">
                <h4 class="font-bold">Module Offerings</h4>
                <p class="text-sm text-gray-500">Manage semester module offerings</p>
            </a>
            <a href="#" class="block p-4 border rounded hover:bg-gray-50 opacity-50 cursor-not-allowed">
                <h4 class="font-bold">Assign Teachers</h4>
                <p class="text-sm text-gray-500">Allocate modules to staff (Admin only)</p>
            </a>
            <a href="#" class="block p-4 border rounded hover:bg-gray-50 opacity-50 cursor-not-allowed">
                <h4 class="font-bold">Result Management</h4>
                <p class="text-sm text-gray-500">View and publish results (Admin only)</p>
            </a>
        </div>
    </div>

@endsection
