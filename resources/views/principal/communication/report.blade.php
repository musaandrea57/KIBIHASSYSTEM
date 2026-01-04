@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Communication Delivery Report</h1>
            <p class="mt-1 text-sm text-gray-600">Audit delivery and engagement metrics.</p>
        </div>
        <div>
            <a href="{{ route('principal.communication.export', ['id' => $item->id, 'type' => $type, 'format' => 'pdf']) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-2">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
            <a href="{{ route('principal.communication.export', ['id' => $item->id, 'type' => $type, 'format' => 'excel']) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
             <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Back
             </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4 mb-6">
        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Recipients</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ count($recipients) }}</dd>
        </div>
        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Successfully Delivered</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ count($recipients) }}</dd> <!-- Assuming instant system delivery -->
        </div>
        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Read / Acknowledged</dt>
            <dd class="mt-1 text-3xl font-semibold text-blue-600">
                {{ $recipients->whereNotNull('read_at')->count() }}
            </dd>
        </div>
        <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Read Rate</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">
                @if(count($recipients) > 0)
                    {{ round(($recipients->whereNotNull('read_at')->count() / count($recipients)) * 100) }}%
                @else
                    0%
                @endif
            </dd>
        </div>
    </div>

    <!-- Recipient List -->
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Recipient Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Delivered At
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Read At
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recipients as $recipient)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $type === 'message' ? $recipient->recipient->name : $recipient->user->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $type === 'message' ? $recipient->recipient->email : $recipient->user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{-- Assuming roles are available via relationship or direct access --}}
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recipient->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recipient->read_at ? $recipient->read_at->format('d M Y, H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recipient->read_at)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Read
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
