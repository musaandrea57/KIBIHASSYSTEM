@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Message') }}
        </h2></div>

    <div class="flex justify-end mb-4">
                <a href="{{ route('messages.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Inbox
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <h1 class="text-2xl font-bold mb-2">{{ $message->subject }}</h1>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <div>
                                <span class="font-semibold">From:</span> {{ $message->sender->name }} &lt;{{ $message->sender->email }}&gt;
                                <br>
                                <span class="font-semibold">To:</span> 
                                @foreach($message->recipients as $recipient)
                                    {{ $recipient->recipient->name }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                            <div>
                                {{ $message->created_at->format('M d, Y H:i') }}
                    </div>

                    <div class="prose max-w-none mb-6">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    @if($message->attachments->count() > 0)
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="font-semibold mb-2">Attachments:</h4>
                            <ul class="list-disc pl-5">
                                @foreach($message->attachments as $attachment)
                                    <li>
                                        <a href="{{ Storage::disk('private')->url($attachment->file_path) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                            {{ $attachment->file_name }} ({{ round($attachment->file_size / 1024, 2) }} KB)
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($isRecipient && !$message->recipients->where('recipient_id', Auth::id())->first()->acknowledged_at)
                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <form action="{{ route('messages.acknowledge', $message) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Acknowledge Receipt
                                </button>
                                <p class="text-xs text-gray-500 mt-2">Click to confirm you have read and understood this message.</p>
                            </form>
                        </div>
                    @elseif($isRecipient)
                         <div class="border-t border-gray-200 pt-4 mt-6">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="-ml-1 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Acknowledged on {{ $message->recipients->where('recipient_id', Auth::id())->first()->acknowledged_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
