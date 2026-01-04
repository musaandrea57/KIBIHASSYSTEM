@extends('layouts.portal')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="bg-red-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Fee Clearance Required
            </h2>
        </div>
        
        <div class="p-8 text-center">
            <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mb-2">Coursework Results Blocked</h3>
            <p class="text-gray-500 mb-8 max-w-lg mx-auto">
                Access to Continuous Assessment results for <strong>{{ $academicYear->year }} - {{ $semester->name }}</strong> is restricted due to outstanding fee payments.
            </p>

            <div class="bg-gray-50 rounded-lg p-6 max-w-md mx-auto mb-8 border border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total Invoiced:</span>
                    <span class="font-medium">{{ number_format($totals['total_invoiced'], 2) }} TZS</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total Paid:</span>
                    <span class="font-medium text-green-600">{{ number_format($totals['total_paid'], 2) }} TZS</span>
                </div>
                <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                    <span class="font-bold text-gray-800">Outstanding Balance:</span>
                    <span class="font-bold text-red-600 text-lg">{{ number_format($totals['total_balance'], 2) }} TZS</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('student.finance.index') }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Go to My Finance
                </a>
                
                <a href="mailto:finance@kibihas.ac.tz" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact Finance
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
