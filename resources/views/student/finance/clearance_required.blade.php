@extends('layouts.portal')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-red-800">Fee Clearance Required</h3>
                    <div class="mt-2 text-red-700">
                        <p>Access to this academic content is restricted because your student account is not fee-cleared for the current semester.</p>
                        <p class="mt-1 font-semibold">Please clear your outstanding balance to proceed.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <p class="text-sm font-medium text-gray-500">Total Outstanding Balance</p>
                <p class="mt-2 text-3xl font-bold text-red-600">
                    {{ number_format($totals['total_balance'], 2) }} TZS
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <p class="text-sm font-medium text-gray-500">Total Invoiced</p>
                <p class="mt-2 text-xl font-semibold text-gray-900">
                    {{ number_format($totals['total_invoiced'], 2) }} TZS
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <p class="text-sm font-medium text-gray-500">Total Paid</p>
                <p class="mt-2 text-xl font-semibold text-green-600">
                    {{ number_format($totals['total_paid'], 2) }} TZS
                </p>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-md font-medium text-gray-900">Outstanding Items Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($breakdown as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item['item_description'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item['invoice_number'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item['due_date'] ? \Carbon\Carbon::parse($item['due_date'])->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ number_format($item['amount'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">
                                {{ number_format($item['paid'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold text-right">
                                {{ number_format($item['balance'], 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No outstanding items found for the current context.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center bg-gray-50 p-6 rounded-lg border border-gray-200">
            <div class="text-sm text-gray-600">
                <p>Please visit the Accounts Office or make a payment to unlock access.</p>
            </div>
            <div class="space-x-4">
                <a href="{{ route('student.finance.invoices') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    View Invoices
                </a>
                <a href="{{ route('student.finance.payments') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    My Payments
                </a>
            </div>
        </div>
    </div>

@endsection
