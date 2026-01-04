@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Send Bulk SMS</h2>
                    <a href="{{ route('admin.communication.sms.templates.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Manage Templates</a>
                </div>

                <form method="POST" action="{{ route('admin.communication.sms.preview') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Target Group -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Target Group</label>
                            <select name="target_group" id="target_group" class="shadow border rounded w-full py-2 px-3 text-gray-700" onchange="toggleFilters()">
                                <option value="custom">Custom Selection (Filter below)</option>
                                <option value="admissions">Admissions: Approved Applicants</option>
                                <option value="finance">Finance: Students with Outstanding Balance</option>
                                <option value="registration">Registration: Not Registered Current Semester</option>
                                <option value="results">Results: Published Results (Fee Cleared)</option>
                                <option value="individual">Individual (Test)</option>
                            </select>
                        </div>

                        <!-- Recipient Type -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Recipient Type</label>
                            <select name="recipient_type" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                <option value="student">Student</option>
                                <option value="application">Applicant</option>
                                <option value="individual">Individual</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Filters -->
                    <div id="filters" class="p-4 bg-gray-50 rounded mb-6">
                        <h3 class="text-sm font-bold text-gray-600 mb-2">Filters</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Program</label>
                                <select name="program_id" class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Academic Year</label>
                                <select name="academic_year_id" class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    <option value="">Current/All</option>
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="individual_input" class="mt-4 hidden">
                             <label class="block text-gray-700 text-xs font-bold mb-1">Phone Number</label>
                             <input type="text" name="phone_number" class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700" placeholder="+255...">
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Message Content</label>
                        
                        <div class="mb-2">
                            <label class="text-xs text-gray-600">Load Template:</label>
                            <select name="template_id" id="template_select" class="shadow border rounded py-1 px-2 text-sm text-gray-700 w-full md:w-1/2" onchange="loadTemplate()">
                                <option value="">-- Select Template --</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-body="{{ $template->message_body }}" data-key="{{ $template->key }}">{{ $template->key }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="template_key" id="template_key">
                        </div>

                        <textarea name="message" id="message_body" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Placeholders: {name}, {balance}, {reg_no}</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Preview & Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const group = document.getElementById('target_group').value;
    const individualInput = document.getElementById('individual_input');
    
    if (group === 'individual') {
        individualInput.classList.remove('hidden');
    } else {
        individualInput.classList.add('hidden');
    }
}

function loadTemplate() {
    const select = document.getElementById('template_select');
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('message_body').value = option.getAttribute('data-body');
        document.getElementById('template_key').value = option.getAttribute('data-key');
    }
}
</script>
@endsection
