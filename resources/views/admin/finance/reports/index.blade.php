@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Finance Overview</h2>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Invoiced</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalInvoiced, 2) }} TZS</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Collected</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalCollected, 2) }} TZS</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Outstanding Balance</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($outstanding, 2) }} TZS</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Payments -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Payments</h3>
                        <a href="{{ route('admin.finance.payments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $payment->student->first_name }} {{ $payment->student->last_name }}
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ number_format($payment->amount, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between">
                                <div class="sm:flex">
                                    <div class="mr-6 flex items-center text-sm text-gray-500">
                                        {{ $payment->payment_reference }}
                                    </div>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    {{ $payment->payment_date->format('d M Y') }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Recent Invoices -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Invoices</h3>
                        <a href="{{ route('admin.finance.invoices.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentInvoices as $invoice)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-indigo-600 truncate">
                                    {{ $invoice->student->first_name }} {{ $invoice->student->last_name }}
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ number_format($invoice->subtotal, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex justify-between">
                                <div class="sm:flex">
                                    <div class="mr-6 flex items-center text-sm text-gray-500">
                                        {{ $invoice->invoice_number }}
                                    </div>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    {{ $invoice->issue_date->format('d M Y') }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 flex justify-center">
                <a href="{{ route('admin.finance.reports.collections', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow hover:bg-gray-700">
                    View Daily Collection Report
                </a>
            </div>

@endsection
