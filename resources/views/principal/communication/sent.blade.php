@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Sent Official Messages</h1>
        <p class="mt-1 text-sm text-gray-600">Audit trail of all dispatched communications.</p>
    </div>

    @include('principal.communication.partials.nav')

    <!-- Search Box -->
    <div class="mb-4 mt-4">
        <form action="{{ route('principal.communication.sent') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sent messages..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Search
            </button>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($messages->count() > 0)
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($messages as $message)
            <li>
                <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $message->subject }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                To: {{ $message->recipients_count }} Recipients | Type: {{ $message->classification }}
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex flex-col items-end space-y-2">
                             <span class="text-xs text-gray-500">
                                {{ $message->created_at->format('d M Y, H:i') }}
                             </span>
                             <div class="flex space-x-2">
                                 <a href="{{ route('principal.communication.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">View</a>
                                 <span class="text-gray-300">|</span>
                                 <a href="{{ route('principal.communication.report', ['id' => $message->id, 'type' => 'message']) }}" class="text-green-600 hover:text-green-900 text-xs font-medium">Delivery Report</a>
                             </div>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-paper-plane text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500 text-sm">No official messages sent yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection
