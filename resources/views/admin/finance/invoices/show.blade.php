@extends('layouts.portal')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Invoice #{{ $invoice->invoice_number }}</h2>
        <div class="space-x-2">
                    @if($invoice->status !== 'voided' && $invoice->payments->where('status', 'posted')->isEmpty())
                        @can('create_invoice')
                        <button type="button" onclick="document.getElementById('void-invoice-modal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Void Invoice
                        </button>
                        @endcan
                    @endif
                    <a href="{{ route('admin.finance.invoices.index') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Void Modal -->
            <div id="void-invoice-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Void Invoice</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to void this invoice? This action cannot be undone.
                            </p>
                            <form action="{{ route('admin.finance.invoices.void', $invoice) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-4 text-left">
                                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                                    <textarea name="reason" id="reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Enter reason for voiding..."></textarea>
                                </div>
                                <div class="flex justify-between mt-4">
                                    <button type="button" onclick="document.getElementById('void-invoice-modal').classList.add('hidden')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Confirm Void</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Header -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Student Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Issued to {{ $invoice->student->first_name }} {{ $invoice->student->last_name }} ({{ $invoice->student->admission_number }})
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($invoice->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 
                               ($invoice->status === 'voided' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Context</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $invoice->program->code }} - NTA Level {{ $invoice->nta_level }}<br>
                                {{ $invoice->academicYear->year }} - {{ $invoice->semester->name }}
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Dates</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                Issued: {{ $invoice->issue_date->format('d M Y') }}<br>
                                Due: <span class="{{ $invoice->due_date->isPast() && $invoice->balance > 0 ? 'text-red-600 font-bold' : '' }}">{{ $invoice->due_date->format('d M Y') }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Line Items</h3>
                </div>
                <div class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">{{ number_format($item->paid_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ number_format($item->balance_amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-bold">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Totals</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">{{ number_format($invoice->total_paid, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">{{ number_format($invoice->balance, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments History -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment History</h3>
                    @if($invoice->balance > 0 && $invoice->status !== 'voided')
                        @can('record_payments')
                        <a href="{{ route('admin.finance.payments.create', ['invoice_id' => $invoice->id]) }}" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                            Record Payment
                        </a>
                        @endcan
                    @endif
                </div>
                <div class="border-t border-gray-200">
                    @if($invoice->payments->isEmpty())
                        <div class="px-6 py-4 text-sm text-gray-500">No payments recorded yet.</div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($payment->method) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                                        <a href="{{ route('admin.finance.payments.show', $payment) }}">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        
    @endsection
