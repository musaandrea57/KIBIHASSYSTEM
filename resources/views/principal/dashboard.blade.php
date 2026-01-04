@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Principal Dashboard') }}</h2>
        <p class="text-gray-600">Overview of institutional performance and activities</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Total Students</div>
            <div class="text-2xl font-bold">{{ $stats['total_students'] }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Teaching Staff</div>
            <div class="text-2xl font-bold">{{ $stats['total_teachers'] }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Total Revenue</div>
            <div class="text-2xl font-bold text-green-600">Tzs {{ number_format($stats['total_revenue']) }}</div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-gray-500 text-sm">Actions</div>
            <div class="mt-2">
                <a href="{{ route('messages.create') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">Send Announcement &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Recent Activity / Payments -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Recent Financial Activity</h3>
        </div>
        <div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stats['recent_payments'] as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($payment->amount) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->transaction_reference }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
        <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold mb-4">Reports</h3>
            <ul class="space-y-2">
                <li><a href="#" class="text-indigo-600 hover:underline">Teacher Performance Report</a></li>
                <li><a href="#" class="text-indigo-600 hover:underline">Student Attendance Report</a></li>
                <li><a href="#" class="text-indigo-600 hover:underline">Financial Summary PDF</a></li>
            </ul>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Communications</h3>
                <a href="{{ route('messages.index') }}" class="block text-indigo-600 hover:underline">Inbox</a>
        </div>
    </div>

@endsection
