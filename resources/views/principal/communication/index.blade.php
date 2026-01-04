@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Principal's Communication Center</h1>
        <p class="mt-1 text-sm text-gray-600">Secure, audit-ready institutional messaging platform.</p>
    </div>

    @include('principal.communication.partials.nav')

    <!-- Search Box -->
    <div class="mb-4 mt-4">
        <form action="{{ route('principal.communication.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inbox..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
                <a href="{{ route('principal.communication.show', $message->id) }}" class="block hover:bg-gray-50">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $message->sender->name ?? 'Unknown Sender' }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $message->read_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $message->classification ?? 'General' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-envelope mr-1.5 text-gray-400"></i>
                                    {{ $message->subject }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <i class="fas fa-calendar mr-1.5 text-gray-400"></i>
                                <p>
                                    Received <time datetime="{{ $message->created_at }}">{{ $message->created_at->diffForHumans() }}</time>
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500 text-sm">No messages in inbox.</p>
        </div>
        @endif
    </div>
</div>
@endsection
