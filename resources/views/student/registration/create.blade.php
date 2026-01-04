@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Registration') }}
        </h2></div>

    @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('student.registration.store') }}" id="registration-form">
                @csrf
                
                <!-- Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Session Information</h3>
                                <p class="mt-1 text-sm text-gray-600">Academic Year: <span class="font-semibold">{{ $deadline->academicYear->name }}</span></p>
                                <p class="text-sm text-gray-600">Semester: <span class="font-semibold">{{ $deadline->semester->name }}</span></p>
                                <p class="text-sm text-gray-600">Deadline: <span class="font-semibold text-red-600">{{ $deadline->end_date->format('M d, Y') }}</span></p>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Credit Rules (NTA Level {{ $student->current_nta_level }})</h3>
                                @if($program)
                                    <p class="mt-1 text-sm text-gray-600">Minimum Credits: <span class="font-semibold" id="min-credits">{{ $program->min_credits_per_semester }}</span></p>
                                    <p class="text-sm text-gray-600">Maximum Credits: <span class="font-semibold" id="max-credits">{{ $program->max_credits_per_semester }}</span></p>
                                @else
                                    <p class="mt-1 text-sm text-yellow-600">No credit rules defined for your level.</p>
                                @endif
                                <div class="mt-2 p-2 bg-gray-100 rounded">
                                    <p class="text-sm font-bold">Selected Credits: <span id="total-credits" class="text-blue-600">0</span></p>
                        </div>
                    </div>
                </div>

                <!-- Modules Selection -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Modules</h3>
                        
                        @if($offerings->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Select</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($offerings as $offering)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" name="offerings[]" value="{{ $offering->id }}" 
                                                        class="module-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        data-credits="{{ $offering->module->credits }}"
                                                        {{ in_array($offering->id, old('offerings', $selectedIds ?? [])) ? 'checked' : '' }}
                                                    >
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $offering->module->code }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $offering->module->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $offering->module->credits }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $offering->is_core ? 'Core' : 'Elective' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No modules found available for registration.</p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end">
                    <button type="submit" name="action" value="save_draft" class="mr-3 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Save Draft
                    </button>
                    <button type="submit" name="action" value="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Submit Registration
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.module-checkbox');
            const totalDisplay = document.getElementById('total-credits');
            const minCredits = parseInt(document.getElementById('min-credits')?.innerText || 0);
            const maxCredits = parseInt(document.getElementById('max-credits')?.innerText || 1000);
            
            function calculateTotal() {
                let total = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        total += parseInt(cb.dataset.credits);
                    }
                });
                totalDisplay.innerText = total;
                
                // Visual feedback
                if (total < minCredits || total > maxCredits) {
                    totalDisplay.classList.remove('text-green-600');
                    totalDisplay.classList.add('text-red-600');
                } else {
                    totalDisplay.classList.remove('text-red-600');
                    totalDisplay.classList.add('text-green-600');
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', calculateTotal);
            });

            // Initial calculation
            calculateTotal();
        });
    </script>

@endsection