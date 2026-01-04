@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Payment Required') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                        <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Access Restricted</h3>
                    <p class="text-gray-600 mb-8">
                        You have outstanding fee balances. Please clear your dues to access academic results and other services.
                    </p>

                    <div class="bg-red-50 p-6 rounded-lg max-w-2xl mx-auto mb-8">
                        <div class="text-sm text-red-600 font-bold uppercase tracking-wide">Total Outstanding Balance</div>
                        <div class="text-4xl font-extrabold text-red-700 mt-2">TZS {{ number_format($balance, 2) }}

                    <h4 class="text-lg font-medium text-gray-900 mb-4">Pending Invoices</h4>
                    <div class="max-w-3xl mx-auto overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @foreach($invoice->items as $item)
                                                <div>{{ $item->description }}</div>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($invoice->paid_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        <p class="text-sm text-gray-500">
                            Payments can be made via Bank Transfer (NMB/CRDB) or Mobile Money using Control Number.
                            <br>Please visit the accounts office if you have already paid.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection