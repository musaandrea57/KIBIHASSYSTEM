@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">SMS Preview</h2>
                    <a href="{{ route('admin.communication.sms.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</a>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- Icon -->
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                You are about to send SMS to <span class="font-bold">{{ $count }}</span> recipients.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-2">Message Sample (First Recipient)</h3>
                    <div class="p-4 bg-gray-100 rounded border border-gray-300">
                        <pre class="whitespace-pre-wrap font-mono text-sm text-gray-800">{{ $renderedMessage }}</pre>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.communication.sms.send') }}">
                    @csrf
                    <!-- Preserve all original request data -->
                    @foreach($request->except(['_token']) as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    
                    <!-- Explicitly pass message_body from textarea or updated logic -->
                    <input type="hidden" name="message_body" value="{{ $messageBody }}">

                    <div class="flex justify-end space-x-4">
                         <a href="{{ route('admin.communication.sms.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Edit</a>
                         <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" onclick="return confirm('Confirm sending {{ $count }} SMS messages? This action cannot be undone.');">Confirm Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
