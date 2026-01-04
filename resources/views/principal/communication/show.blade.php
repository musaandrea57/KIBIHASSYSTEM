@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Official Communication Record</h1>
            <p class="mt-1 text-sm text-gray-600">Reference ID: MSG-{{ str_pad($message->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
             <a href="{{ route('principal.communication.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Back to Inbox
             </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $message->subject }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Classification: <span class="font-semibold text-gray-700">{{ $message->classification }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">
                        Dispatched: {{ $message->created_at->format('d M Y, H:i') }}
                    </p>
                     <p class="text-sm text-gray-500">
                        From: <span class="font-medium text-gray-900">{{ $message->sender->name ?? 'Unknown' }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="prose max-w-none text-gray-800 font-serif">
                {!! nl2br(e($message->body)) !!}
            </div>

            @if($message->attachments->count() > 0)
            <div class="mt-8 border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Attachments</h4>
                <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                    @foreach($message->attachments as $attachment)
                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                        <div class="w-0 flex-1 flex items-center">
                            <i class="fas fa-paperclip text-gray-400 flex-shrink-0 h-5 w-5"></i>
                            <span class="ml-2 flex-1 w-0 truncate">
                                {{ $attachment->file_name }}
                            </span>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Download
                            </a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200 flex justify-between">
            <div class="text-xs text-gray-500">
                This is an official institutional record. Do not delete or alter.
            </div>
            @if($message->sender_id === auth()->id())
            <div>
                 <a href="{{ route('principal.communication.report', ['id' => $message->id, 'type' => 'message']) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    View Delivery Report
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Replies -->
    @if($message->replies->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Conversation History</h3>
        <div class="space-y-4">
            @foreach($message->replies as $reply)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                     <div class="flex justify-between items-start">
                        <div>
                             <p class="text-sm font-medium text-gray-900">{{ $reply->sender->name ?? 'Unknown' }}</p>
                             <p class="text-xs text-gray-500">{{ $reply->created_at->format('d M Y, H:i') }}</p>
                        </div>
                     </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                     <div class="prose max-w-none text-gray-800 font-serif">
                        {!! nl2br(e($reply->body)) !!}
                    </div>
                     @if($reply->attachments->count() > 0)
                        <div class="mt-4 border-t border-gray-200 pt-2">
                             <h5 class="text-xs font-medium text-gray-500 mb-1">Attachments</h5>
                             <ul class="list-disc pl-5 text-sm">
                                @foreach($reply->attachments as $att)
                                    <li><a href="{{ Storage::url($att->file_path) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">{{ $att->file_name }}</a></li>
                                @endforeach
                             </ul>
                        </div>
                     @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Reply Form -->
    <div class="mt-8 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Send Reply</h3>
            <form action="{{ route('principal.communication.reply', $message->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-700">Message</label>
                        <div class="mt-1">
                            <textarea id="body" name="body" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required placeholder="Type your reply here..."></textarea>
                        </div>
                    </div>
                    
                    <div>
                         <label class="block text-sm font-medium text-gray-700">Attachments (Optional)</label>
                         <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload files</span>
                                        <input id="file-upload" name="attachments[]" type="file" class="sr-only" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS up to 10MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-reply mr-2"></i> Send Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
