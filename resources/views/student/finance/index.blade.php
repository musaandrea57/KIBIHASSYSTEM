@extends('layouts.portal')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'payments' }">

    <!-- A) Finance Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">My Finance</h2>
            <div class="mt-1 flex items-center space-x-3 text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                    {{ $active_year->year ?? 'N/A' }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ $active_semester->name ?? 'N/A' }}
                </span>
                <span class="text-gray-400">|</span>
                <span>Last updated {{ $last_updated->diffForHumans() }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('student.finance.statement') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="mr-2 -ml-1 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Statement
            </a>
            <a href="{{ route('student.finance.payment_info') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="mr-2 -ml-1 h-5 w-5 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Payment Instructions
            </a>
        </div>
    </div>

    <!-- B) Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-finance.metric-card title="Total Invoiced" amount="{{ $totals['invoiced'] }}" color="blue">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </x-finance.metric-card>

        <x-finance.metric-card title="Total Paid" amount="{{ $totals['paid'] }}" color="green">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-finance.metric-card>

        <x-finance.metric-card title="Outstanding Balance" amount="{{ $totals['outstanding'] }}" color="{{ $totals['outstanding'] > 0 ? 'red' : 'gray' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-finance.metric-card>
    </div>

    <!-- C) Installment Plan & Due Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Installment Plan</h3>
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Academic Year {{ $active_year->year ?? '' }}</span>
        </div>
        
        <div class="p-6 flex-1 flex flex-col justify-between">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach(['oct' => 'Installment I', 'jan' => 'Installment II', 'apr' => 'Installment III'] as $key => $label)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-gray-500 uppercase">{{ $label }}</span>
                            <x-finance.status-badge :status="$installments[$key]['status']" />
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Required:</span>
                                <span class="font-medium text-gray-900">{{ number_format($installments[$key]['required']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Paid:</span>
                                <span class="font-medium text-green-600">{{ number_format($installments[$key]['paid']) }}</span>
                            </div>
                            @if($installments[$key]['remaining'] > 0)
                                <div class="flex justify-between text-sm border-t border-gray-200 pt-1 mt-1">
                                    <span class="text-gray-500 font-medium">Due:</span>
                                    <span class="font-bold text-red-600">{{ number_format($installments[$key]['remaining']) }}</span>
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('student.finance.print_installment', ['installment' => $key]) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                                        <svg class="mr-1.5 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download Bill
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                @if($next_due)
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Next Due: <span class="font-bold text-gray-900">{{ $next_due['name'] }}</span></span>
                            <span class="text-xs text-gray-500 ml-2">by {{ $next_due['due_date'] }}</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($next_due['amount'], 2) }} TZS</span>
                    </div>
                @endif
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Fee Payment Progress</span>
                    <span>{{ number_format($progress_percentage, 1) }}%</span>
                </div>
                <x-finance.progress-bar :percentage="$progress_percentage" color="{{ $progress_percentage == 100 ? 'green' : 'blue' }}" />
            </div>
        </div>
    </div>

    <!-- D) Invoices Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Invoices</h3>
            <div class="flex gap-2">
                <!-- Simple Filter Placeholders -->
                <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option>All Years</option>
                </select>
                <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option>All Statuses</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invoice->academicYear->year ?? '' }} - {{ $invoice->semester->name ?? '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">{{ number_format($invoice->total_paid, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium text-right">{{ number_format($invoice->balance, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <x-finance.status-badge :status="$invoice->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('student.finance.invoice.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- E) Payments & Receipts -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex" aria-label="Tabs">
                <button @click="activeTab = 'payments'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'payments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'payments' }" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                    Payments History
                </button>
                <button @click="activeTab = 'receipts'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'receipts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'receipts' }" class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                    Receipts
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Payments Tab -->
            <div x-show="activeTab === 'payments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                 <div class="flex justify-between mb-4">
                     <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Recent Payments</h4>
                     <!-- Search placeholder -->
                     <div class="relative rounded-md shadow-sm">
                        <input type="text" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 sm:text-sm border-gray-300 rounded-md" placeholder="Search reference...">
                     </div>
                 </div>
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allocated To</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->transaction_reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->invoice ? $payment->invoice->invoice_number : 'Unallocated' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-finance.status-badge :status="$payment->status" />
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No payments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>
            </div>

            <!-- Receipts Tab -->
            <div x-show="activeTab === 'receipts'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="flex justify-between mb-4">
                     <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Available Receipts</h4>
                 </div>
                 <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Download</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($payments->where('status', 'posted') as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">RCT-{{ $payment->transaction_reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('student.finance.receipt', $payment) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="mr-1.5 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No receipts available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>
            </div>
        </div>
    </div>

    <!-- F) Alerts & Guidance -->
    <div class="space-y-6">
        @if(!$is_cleared && $totals['outstanding'] > 0)
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Payment Required</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>Your outstanding balance is <strong>{{ number_format($totals['outstanding'], 2) }} TZS</strong>. Please complete payment to access your results and clearance services.</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($is_cleared)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Fee Cleared</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>You are fully fee-cleared for this semester. All academic services are accessible.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6 flex flex-col justify-center">
             <div class="text-center">
                 <div class="mx-auto h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                     <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                     </svg>
                 </div>
                 <h3 class="text-lg font-medium text-gray-900">Need Assistance?</h3>
                 <p class="mt-2 text-sm text-gray-500">Contact the Finance Office for billing inquiries or reconciliation issues.</p>
                 <div class="mt-6 space-y-3">
                     <a href="mailto:finance@kibihas.ac.tz" class="flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                         finance@kibihas.ac.tz
                     </a>
                     <a href="tel:+255123456789" class="flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                         +255 123 456 789
                     </a>
                 </div>
             </div>
        </div>
    </div>

</div>
@endsection