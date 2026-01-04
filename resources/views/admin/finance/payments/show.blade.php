@extends('layouts.portal')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Payment {{ $payment->payment_reference }}</h1>
        <div class="space-x-2">
                    @can('download_receipts')
                    <a href="{{ route('admin.finance.payments.receipt', $payment) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Download Receipt
                    </a>
                    @endcan
                    
                    <a href="{{ route('admin.finance.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Back
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Status Information</h2>
                            <p class="text-sm text-gray-500">Date: {{ $payment->payment_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                {{ $payment->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Student Information</h3>
                            <p class="text-gray-900 font-medium">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</p>
                            <p class="text-gray-600">{{ $payment->student->admission_number }}</p>
                            <p class="text-gray-600">{{ $payment->student->program->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Payment Details</h3>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                                <dt class="text-sm font-medium text-gray-500">Amount:</dt>
                                <dd class="text-sm font-bold text-gray-900">{{ number_format($payment->amount, 2) }} TZS</dd>
                                
                                <dt class="text-sm font-medium text-gray-500">Method:</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</dd>
                                
                                <dt class="text-sm font-medium text-gray-500">Transaction Ref:</dt>
                                <dd class="text-sm text-gray-900">{{ $payment->transaction_ref ?? 'N/A' }}</dd>
                                
                                <dt class="text-sm font-medium text-gray-500">Received By:</dt>
                                <dd class="text-sm text-gray-900">{{ $payment->receivedBy->name ?? 'System' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Allocation to Invoice #{{ $payment->invoice->invoice_number }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Item / Description</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Allocated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payment->allocations as $allocation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $allocation->invoiceItem->feeItem->name ?? $allocation->invoiceItem->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                {{ number_format($allocation->amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-6 py-4 text-right font-bold text-gray-700">Total Allocated:</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">{{ number_format($payment->allocations->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($payment->status === 'posted')
                @can('reverse_payments')
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-red-200">
                    <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                        <h3 class="text-lg font-semibold text-red-800">Danger Zone: Reverse Payment</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Reversing a payment will mark it as reversed and deduct the amount from the invoice's paid total. This action cannot be undone.
                        </p>
                        <form action="{{ route('admin.finance.payments.reverse', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to REVERSE this payment? This action is irreversible.')">
                            @csrf
                            <div class="mb-4">
                                <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Reversal</label>
                                <textarea name="reason" id="reason" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="e.g. Entered in error, cheque bounced"></textarea>
                            </div>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Reverse Payment
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            @elseif($payment->status === 'reversed')
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <h3 class="text-lg font-semibold text-red-800 mb-2">Reversal Details</h3>
                    <p class="text-sm text-red-700"><strong>Reversed By:</strong> {{ $payment->reversedBy->name ?? 'Unknown' }}</p>
                    <p class="text-sm text-red-700"><strong>Date:</strong> {{ $payment->reversed_at->format('d M Y H:i') }}</p>
                    <p class="text-sm text-red-700"><strong>Reason:</strong> {{ $payment->reversed_reason }}</p>
                </div>
            
        
    @endsection
