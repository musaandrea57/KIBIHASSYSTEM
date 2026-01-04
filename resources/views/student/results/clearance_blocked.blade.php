@extends('layouts.portal')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-red-50 border-l-4 border-red-500 p-8 rounded-md shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-6">
                <h3 class="text-2xl font-bold text-red-800">Fee Clearance Required</h3>
                <div class="mt-4 text-lg text-red-700">
                    <p>Access to your results is currently blocked due to outstanding fee payments.</p>
                    <p class="mt-2">Please clear your tuition fees to view your Semester I and Semester II results.</p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('student.finance.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Go to Finance / Payments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
