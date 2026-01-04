@extends('layouts.portal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Marks Entry Center</h1>
            <p class="text-gray-500">Manage and submit student marks for the current semester.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm">
                <span class="text-gray-500">Current Session:</span>
                <span class="font-medium text-gray-900">{{ $currentYear->name ?? 'N/A' }}</span>
             </div>
        </div>
    </div>

    <!-- Teacher Summary -->
    <div class="bg-white rounded-lg shadow p-6 border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ $teacher->first_name }} {{ $teacher->last_name }}</h3>
                <p class="text-sm text-gray-500">{{ $teacher->staff_number ?? 'Staff No: N/A' }}</p>
            </div>
            <div class="ml-auto flex gap-4 text-sm">
                 <div class="text-center">
                    <span class="block text-2xl font-bold text-gray-900">{{ $offerings->count() }}</span>
                    <span class="text-gray-500">Modules</span>
                 </div>
                 <div class="text-center border-l pl-4">
                    <span class="block text-2xl font-bold text-green-600">{{ $offerings->where('status', 'published')->count() }}</span>
                    <span class="text-gray-500">Published</span>
                 </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-4">
        <input type="text" placeholder="Search modules..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option>All Programmes</option>
        </select>
        <select class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option>All Statuses</option>
        </select>
    </div>

    <!-- Offerings Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($offerings as $offering)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            {{ $offering->module_code }}
                        </span>
                        <span class="ml-2 inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                            NTA Level {{ $offering->nta_level }}
                        </span>
                    </div>
                    @php
                        $statusColors = [
                            'draft' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
                            'pending_admin_approval' => 'bg-orange-50 text-orange-800 ring-orange-600/20',
                            'published' => 'bg-green-50 text-green-800 ring-green-600/20',
                            'active' => 'bg-blue-50 text-blue-800 ring-blue-600/20',
                        ];
                        $statusClass = $statusColors[$offering->status] ?? 'bg-gray-50 text-gray-600 ring-gray-500/10';
                    @endphp
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $offering->status)) }}
                    </span>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2" title="{{ $offering->module_name }}">
                    {{ $offering->module_name }}
                </h3>
                
                <p class="text-sm text-gray-500 mb-4">{{ $offering->semester }}</p>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Completion</span>
                        <span class="font-medium text-gray-900">
                            @if($offering->enrolled_count > 0)
                                {{ round(($offering->marked_count / $offering->enrolled_count) * 100) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $offering->enrolled_count > 0 ? ($offering->marked_count / $offering->enrolled_count) * 100 : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $offering->marked_count }} / {{ $offering->enrolled_count }} Students Graded</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('teacher.marks.show', $offering->id) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enter Marks
                    </a>
                    <!-- Import Button Trigger (Modal) -->
                    <button type="button" class="inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="Import from Excel">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200 border-dashed">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No Modules Assigned</h3>
            <p class="mt-1 text-sm text-gray-500">You haven't been assigned any modules for this academic session yet.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
