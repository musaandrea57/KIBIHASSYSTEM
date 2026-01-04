<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="{{ route('principal.student-performance.index') }}" 
           class="{{ request()->routeIs('principal.student-performance.index') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Overview
        </a>

        <a href="{{ route('principal.student-performance.programme') }}" 
           class="{{ request()->routeIs('principal.student-performance.programme') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Programme Performance
        </a>

        <a href="{{ route('principal.student-performance.cohort') }}" 
           class="{{ request()->routeIs('principal.student-performance.cohort') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Cohort Analysis
        </a>

        <a href="{{ route('principal.student-performance.at-risk') }}" 
           class="{{ request()->routeIs('principal.student-performance.at-risk') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            At-Risk Students
        </a>
    </nav>
</div>
