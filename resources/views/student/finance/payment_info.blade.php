@extends('layouts.portal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Payment Instructions</h2>
        <a href="{{ route('student.finance.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 bg-blue-50 border-b border-blue-100">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-900">How to Pay</h3>
                    <p class="mt-1 text-sm text-blue-700">
                        KIBIHAS accepts payments via Bank Deposit (NMB/CRDB) and Mobile Money (M-Pesa, Tigo Pesa, Airtel Money).
                        Always use your unique <strong>Control Number</strong> when making payments.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="p-6 space-y-8">
            <!-- Method 1: Bank -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-gray-100 text-gray-600 rounded-full h-6 w-6 flex items-center justify-center text-xs mr-2">1</span>
                        Bank Deposit (NMB/CRDB)
                    </h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 ml-2">
                        <li>Visit any NMB or CRDB branch or agent.</li>
                        <li>Request to make a <strong>Control Number Payment</strong>.</li>
                        <li>Provide your Control Number: <span class="font-mono bg-yellow-100 px-2 py-0.5 rounded font-bold text-gray-900">991234567890</span> (Example)</li>
                        <li>Verify the name appears as <strong>KIBIHAS - [Your Name]</strong>.</li>
                        <li>Complete payment and keep the receipt.</li>
                    </ol>
                </div>

                <!-- Method 2: Mobile -->
                <div>
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-gray-100 text-gray-600 rounded-full h-6 w-6 flex items-center justify-center text-xs mr-2">2</span>
                        Mobile Money
                    </h4>
                    <ul class="space-y-3 text-sm text-gray-600 ml-2">
                        <li>
                            <strong>M-Pesa / Tigo Pesa / Airtel Money:</strong>
                            <div class="mt-1 pl-4 border-l-2 border-gray-200">
                                1. Dial USSD Menu (*150*...)<br>
                                2. Select <strong>Pay Bill / Government Payments</strong><br>
                                3. Enter Control Number: <span class="font-mono bg-yellow-100 px-2 py-0.5 rounded font-bold text-gray-900">991234567890</span><br>
                                4. Enter Amount<br>
                                5. Enter PIN to confirm
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h4 class="font-bold text-gray-800 mb-4">Your Active Control Numbers</h4>
                <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Control Number</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Due</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Invoice #{{ $invoice->invoice_number }}
                                        <div class="text-xs text-gray-500">{{ $invoice->academicYear->year ?? '' }} {{ $invoice->semester->name ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-blue-600">{{ $invoice->invoice_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">{{ number_format($invoice->balance, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 capitalize">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No active invoices requiring payment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    * If you don't see a control number, please contact the finance office to generate one.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
