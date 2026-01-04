<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 sticky top-0 z-10">
    <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap gap-4 items-end">
        
        <!-- Academic Year -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Academic Year</label>
            <select name="academic_year_id" class="form-select text-sm rounded-md border-gray-300 w-full sm:w-40 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Years</option>
                @foreach($filterOptions['academic_years'] as $year)
                    <option value="{{ $year->id }}" {{ ($filters['academic_year_id'] ?? '') == $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Semester -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
            <select name="semester_id" class="form-select text-sm rounded-md border-gray-300 w-full sm:w-40 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Semesters</option>
                @foreach($filterOptions['semesters'] as $semester)
                    <option value="{{ $semester->id }}" {{ ($filters['semester_id'] ?? '') == $semester->id ? 'selected' : '' }}>
                        {{ $semester->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Program -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Programme</label>
            <select name="program_id" class="form-select text-sm rounded-md border-gray-300 w-full sm:w-48 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Programmes</option>
                @foreach($filterOptions['programs'] as $program)
                    <option value="{{ $program->id }}" {{ ($filters['program_id'] ?? '') == $program->id ? 'selected' : '' }}>
                        {{ $program->code }} - {{ $program->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- NTA Level -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">NTA Level</label>
            <select name="nta_level" class="form-select text-sm rounded-md border-gray-300 w-full sm:w-32 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Levels</option>
                <option value="4" {{ ($filters['nta_level'] ?? '') == '4' ? 'selected' : '' }}>Level 4</option>
                <option value="5" {{ ($filters['nta_level'] ?? '') == '5' ? 'selected' : '' }}>Level 5</option>
                <option value="6" {{ ($filters['nta_level'] ?? '') == '6' ? 'selected' : '' }}>Level 6</option>
            </select>
        </div>

        <!-- Department -->
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
            <select name="department_id" class="form-select text-sm rounded-md border-gray-300 w-full sm:w-40 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Departments</option>
                @foreach($filterOptions['departments'] as $dept)
                    <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex space-x-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-filter mr-1"></i> Apply
            </button>
            
            <a href="{{ url()->current() }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none">
                Reset
            </a>
            
            <div class="relative group">
                <button type="button" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
                <div class="absolute right-0 w-48 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 ring-1 ring-black ring-opacity-5">
                    <div class="py-1">
                        <a href="{{ route('principal.reports.export', array_merge($filters, ['format' => 'pdf', 'report' => request()->segment(3) ?? 'overview'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-2 text-red-500"></i> Download PDF
                        </a>
                        <a href="{{ route('principal.reports.export', array_merge($filters, ['format' => 'excel', 'report' => request()->segment(3) ?? 'overview'])) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2 text-green-500"></i> Download Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
