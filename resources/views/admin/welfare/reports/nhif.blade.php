@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('NHIF Compliance Report') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filters -->
                    <div class="mb-4 flex space-x-2">
                        <a href="{{ route('admin.welfare.reports.nhif') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">All</a>
                        <a href="{{ route('admin.welfare.reports.nhif', ['status' => 'expired']) }}" class="px-4 py-2 bg-red-200 text-red-800 rounded hover:bg-red-300">Expired</a>
                        <a href="{{ route('admin.welfare.reports.nhif', ['status' => 'expiring_soon']) }}" class="px-4 py-2 bg-yellow-200 text-yellow-800 rounded hover:bg-yellow-300">Expiring Soon</a>
                        <a href="{{ route('admin.welfare.reports.nhif', ['status' => 'pending']) }}" class="px-4 py-2 bg-blue-200 text-blue-800 rounded hover:bg-blue-300">Pending Verification</a>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NHIF Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($memberships as $membership)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $membership->student->first_name }} {{ $membership->student->last_name }}<br>
                                        <span class="text-xs text-gray-500">{{ $membership->student->admission_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $membership->nhif_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $membership->status == 'verified' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $membership->status == 'pending_verification' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $membership->status == 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $membership->status == 'rejected' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $membership->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $membership->expiry_date ? $membership->expiry_date->format('Y-m-d') : 'N/A' }}
                                        @if($membership->expiry_date && $membership->expiry_date < now()->addDays(30) && $membership->expiry_date > now())
                                            <span class="text-red-500 font-bold text-xs ml-1">(Expiring Soon)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $membership->student->program->code ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $memberships->withQueryString()->links() }}
            </div>
        </div>
    </div>

@endsection