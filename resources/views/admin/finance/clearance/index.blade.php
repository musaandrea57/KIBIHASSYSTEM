@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fee Clearance Status') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.finance.clearance.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="academic_year_id" :value="__('Academic Year')" />
                            <select id="academic_year_id" name="academic_year_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="semester_id" :value="__('Semester')" />
                            <select id="semester_id" name="semester_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Semesters</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <x-input-label for="program_id" :value="__('Program')" />
                            <select id="program_id" name="program_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Programs</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Statuses</option>
                                <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>Cleared</option>
                                <option value="not_cleared" {{ request('status') == 'not_cleared' ? 'selected' : '' }}>Not Cleared</option>
                                <option value="overridden" {{ request('status') == 'overridden' ? 'selected' : '' }}>Overridden</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <x-primary-button>
                                {{ __('Filter') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Context</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Invoiced</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($clearanceStatuses as $status)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $status->student->first_name }} {{ $status->student->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $status->student->registration_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $status->student->program->code }} (NTA {{ $status->student->current_nta_level }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $status->academicYear->year }} {{ $status->semester->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ number_format($status->total_invoiced, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">
                                        {{ number_format($status->total_paid, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold text-right">
                                        {{ number_format($status->outstanding_balance, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($status->status === 'cleared')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Cleared
                                            </span>
                                        @elseif($status->status === 'overridden')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Overridden
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Not Cleared
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $status->calculated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($status->status === 'not_cleared')
                                            <button 
                                                onclick="openOverrideModal({{ $status->student_id }}, {{ $status->academic_year_id }}, {{ $status->semester_id }}, '{{ $status->student->first_name }} {{ $status->student->last_name }}')"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Override
                                            </button>
                                        @elseif($status->status === 'overridden')
                                            @php
                                                // Find the active override
                                                $activeOverride = \App\Models\FeeClearanceOverride::where('student_id', $status->student_id)
                                                    ->where('academic_year_id', $status->academic_year_id)
                                                    ->where('semester_id', $status->semester_id)
                                                    ->whereNull('revoked_at')
                                                    ->where('expires_at', '>', now())
                                                    ->first();
                                            @endphp
                                            @if($activeOverride)
                                                <form action="{{ route('admin.finance.clearance.override.revoke', $activeOverride->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to revoke this override?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Revoke</button>
                                                </form>
                                                <span class="text-gray-400 text-xs block">Expires: {{ $activeOverride->expires_at->format('Y-m-d') }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No clearance records found matching the filters.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $clearanceStatuses->links() }}
            </div>
        </div>
    </div>

    <!-- Override Modal -->
    <div id="overrideModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeOverrideModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.finance.clearance.override.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Grant Fee Clearance Override
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Grant temporary clearance for <span id="studentName" class="font-bold"></span>.
                                    </p>
                                    <input type="hidden" name="student_id" id="modal_student_id">
                                    <input type="hidden" name="academic_year_id" id="modal_academic_year_id">
                                    <input type="hidden" name="semester_id" id="modal_semester_id">
                                    
                                    <div class="mt-4">
                                        <x-input-label for="reason" :value="__('Reason for Override')" />
                                        <textarea id="reason" name="reason" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                                    </div>

                                    <div class="mt-4">
                                        <x-input-label for="expires_at" :value="__('Expires At')" />
                                        <input type="date" id="expires_at" name="expires_at" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Grant Override
                        </button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeOverrideModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openOverrideModal(studentId, yearId, semesterId, studentName) {
            document.getElementById('modal_student_id').value = studentId;
            document.getElementById('modal_academic_year_id').value = yearId;
            document.getElementById('modal_semester_id').value = semesterId;
            document.getElementById('studentName').innerText = studentName;
            document.getElementById('overrideModal').classList.remove('hidden');
        }

        function closeOverrideModal() {
            document.getElementById('overrideModal').classList.add('hidden');
        }
    </script>

@endsection
