@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Results') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if(empty($students))
                        <!-- Step 1: Select Context -->
                        <form method="GET" action="{{ route('admin.results.create') }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Program</label>
                                    <select name="program_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required onchange="this.form.submit()">
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if(request('program_id'))
                                    @php
                                        $selectedProgram = $programs->find(request('program_id'));
                                        $modules = \App\Models\Module::where('program_id', request('program_id'))->get();
                                    @endphp
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Module</label>
                                        <select name="module_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
                                            <option value="">Select Module</option>
                                            @foreach($modules as $mod)
                                                <option value="{{ $mod->id }}" {{ request('module_id') == $mod->id ? 'selected' : '' }}>
                                                    {{ $mod->code }} - {{ $mod->name }} (NTA {{ $mod->nta_level }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Semester</label>
                                        <select name="semester_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
                                            <option value="">Select Semester</option>
                                            @foreach(\App\Models\Semester::all() as $sem)
                                                <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                                    {{ $sem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            
                            @if(request('program_id'))
                                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded">
                                    Load Student List
                                </button>
                            @endif
                        </form>
                    @else
                        <!-- Step 2: Input Scores -->
                        <div class="mb-6 bg-blue-50 p-4 rounded-md">
                            <h3 class="font-bold text-lg text-blue-800">Entering Results for: {{ $module->name }} ({{ $module->code }})</h3>
                            <p class="text-sm text-blue-600">Semester: {{ $semester->name }}</p>
                        </div>

                        <form action="{{ route('admin.results.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="module_id" value="{{ $module->id }}">
                            <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                            <input type="hidden" name="academic_year_id" value="{{ \App\Models\AcademicYear::where('is_active', true)->value('id') ?? 1 }}">

                            <div class="overflow-x-auto mb-6">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reg No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CA Score (40%)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Score (60%)</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($students as $student)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $student->registration_number }}
                                                    <input type="hidden" name="results[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $student->user->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="number" name="results[{{ $student->id }}][ca_score]" min="0" max="40" step="0.1" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 ca-input" data-id="{{ $student->id }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="number" name="results[{{ $student->id }}][exam_score]" min="0" max="60" step="0.1" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 exam-input" data-id="{{ $student->id }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 total-score" id="total-{{ $student->id }}">
                                                    0
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center justify-end">
                                <a href="{{ route('admin.results.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded">
                                    Submit Results
                                </button>
                            </div>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const inputs = document.querySelectorAll('.ca-input, .exam-input');
                                inputs.forEach(input => {
                                    input.addEventListener('input', updateTotal);
                                });

                                function updateTotal(e) {
                                    const studentId = e.target.dataset.id;
                                    const caInput = document.querySelector(`.ca-input[data-id="${studentId}"]`);
                                    const examInput = document.querySelector(`.exam-input[data-id="${studentId}"]`);
                                    const totalDisplay = document.getElementById(`total-${studentId}`);

                                    const ca = parseFloat(caInput.value) || 0;
                                    const exam = parseFloat(examInput.value) || 0;
                                    const total = ca + exam;

                                    totalDisplay.textContent = total.toFixed(1);
                                    
                                    if (total >= 40) {
                                        totalDisplay.classList.remove('text-red-600');
                                        totalDisplay.classList.add('text-green-600');
                                    } else {
                                        totalDisplay.classList.remove('text-green-600');
                                        totalDisplay.classList.add('text-red-600');
                                    }
                                }
                            });
                        </script>
                    @endif
        </div>
    </div>

@endsection