@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Finance') }}
        </h2></div>

    <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Total Invoiced</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalInvoiced, 2) }} TZS
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Total Paid</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalPaid, 2) }} TZS</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500">Outstanding Balance</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ number_format($outstandingBalance, 2) }} TZS</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Invoices -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">Recent Invoices</h3>
                        <a href="{{ route('student.finance.invoices') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                    </div>
                    <div class="p-6">
                        @if($recentInvoices->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inv #</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentInvoices as $invoice)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $invoice->issue_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                                           ($invoice->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($invoice->status === 'voided' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                        {{ ucfirst($invoice->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No invoices found.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800">Recent Payments</h3>
                        <a href="{{ route('student.finance.payments') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                    </div>
                    <div class="p-6">
                        @if($recentPayments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ref #</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentPayments as $payment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $payment->transaction_reference }}</td>
                                                <td class="px-4 py-3 text-sm text-green-600 font-bold text-right">{{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <a href="{{ route('student.finance.receipt', $payment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No payments found.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
