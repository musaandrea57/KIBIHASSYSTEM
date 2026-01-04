<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-2 items-center">
        <!-- Search -->
        <div class="flex-grow min-w-[200px]">
            <div class="relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" name="search" id="search" placeholder="Search Student..." value="{{ $filters['search'] ?? '' }}" class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>

        <!-- Academic Year -->
        <div class="w-40">
            <select name="academic_year_id" id="academic_year_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($options['academic_years'] as $year)
                    <option value="{{ $year->id }}" {{ (isset($filters['academic_year_id']) && $filters['academic_year_id'] == $year->id) ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Semester -->
        <div class="w-32">
            <select name="semester_id" id="semester_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($options['semesters'] as $semester)
                    <option value="{{ $semester->id }}" {{ (isset($filters['semester_id']) && $filters['semester_id'] == $semester->id) ? 'selected' : '' }}>
                        {{ $semester->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Programme -->
        <div class="w-48">
            <select name="program_id" id="program_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Programmes</option>
                @foreach($options['programs'] as $program)
                    <option value="{{ $program->id }}" {{ (isset($filters['program_id']) && $filters['program_id'] == $program->id) ? 'selected' : '' }}>
                        {{ $program->code }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Department -->
        <div class="w-40">
            <select name="department_id" id="department_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Depts</option>
                @foreach($options['departments'] as $dept)
                    <option value="{{ $dept->id }}" {{ (isset($filters['department_id']) && $filters['department_id'] == $dept->id) ? 'selected' : '' }}>
                        {{ $dept->code ?? substr($dept->name, 0, 10) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-filter mr-2"></i> Filter
        </button>

        <!-- Reset -->
        @if(collect($filters)->except(['page', 'academic_year_id', 'semester_id'])->isNotEmpty())
            <a href="{{ url()->current() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset
            </a>
        @endif
    </form>
</div>
