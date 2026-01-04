@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Invoices') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-gray-800">Invoice History</h3>
                </div>
                <div class="p-6">
                    @if($invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('d M Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->academicYear->year }} ({{ $invoice->semester->name }})</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">{{ number_format($invoice->total_paid, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right font-bold">{{ number_format($invoice->balance, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                                       ($invoice->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($invoice->status === 'voided' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <!-- Invoice Items Row (Optional, simplified here) -->
                                        @if($invoice->items->count() > 0)
                                            <tr>
                                                <td colspan="8" class="px-6 py-2 bg-gray-50">
                                                    <div class="text-xs text-gray-500">
                                                        <strong>Details:</strong>
                                                        @foreach($invoice->items as $item)
                                                            <span class="mr-4">{{ $item->description }} ({{ number_format($item->amount, 2) }})</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No invoices found.</p>
                    @endif
        </div>
    </div>

@endsection
