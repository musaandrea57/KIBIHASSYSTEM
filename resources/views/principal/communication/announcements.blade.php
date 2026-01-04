@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Institutional Announcements</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and track public communications.</p>
        </div>
        <a href="{{ route('principal.communication.create', ['type' => 'announcement']) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-bullhorn mr-2"></i> New Announcement
        </a>
    </div>

    @include('principal.communication.partials.nav')

    <!-- Search & Filter -->
    <div class="mb-4 mt-4 flex gap-4">
        <form action="{{ route('principal.communication.announcements') }}" method="GET" class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search announcements..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <select name="status" onchange="this.form.submit()" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-40 sm:text-sm border-gray-300 rounded-md">
                <option value="">All Active</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Drafts</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($announcements->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($announcements as $announcement)
            <li>
                <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $announcement->title }}
                                </p>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $announcement->status_color }}-100 text-{{ $announcement->status_color }}-800">
                                    {{ $announcement->status_label }}
                                </span>
                                @if($announcement->priority === 'urgent')
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Urgent
                                </span>
                                @endif
                            </div>
                            <div class="mt-2 flex">
                                <div class="flex items-center text-sm text-gray-500 mr-6">
                                    <i class="fas fa-folder mr-1.5 text-gray-400"></i>
                                    {{ $announcement->category }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-users mr-1.5 text-gray-400"></i>
                                    {{ is_array($announcement->audience) ? implode(', ', $announcement->audience) : 'All' }}
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex flex-col items-end space-y-2">
                             <span class="text-xs text-gray-500">
                                {{ $announcement->created_at->format('d M Y, H:i') }}
                             </span>
                             <div class="flex space-x-3">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">View</a>
                                <span class="text-gray-300">|</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Edit</a>
                             </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $announcements->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-bullhorn text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500 text-sm">No announcements found.</p>
        </div>
        @endif
    </div>
</div>
@endsection
