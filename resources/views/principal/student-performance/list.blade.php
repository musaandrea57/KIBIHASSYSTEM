@extends('layouts.portal')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Student List</h1>
        <p class="mt-2 text-sm text-gray-600">Browse all students and their performance metrics.</p>
    </div>

    <!-- Filter Bar -->
    @include('principal.student-performance.partials.filter-bar')

    <!-- Main Content -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Students</h2>
            <div class="text-sm text-gray-500">
                Total: {{ $students->total() }}
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programme</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">NTA Level</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Weighted GPA</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->registration_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->program->code }}</div>
                                <div class="text-sm text-gray-500" title="{{ $student->program->name }}">{{ Str::limit($student->program->name, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $student->current_nta_level }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($student->gpa !== null)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->gpa >= 3.5 ? 'bg-green-100 text-green-800' : ($student->gpa >= 2.0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($student->gpa, 2) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('principal.student-performance.show', $student->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-gray-100 rounded-full mb-3">
                                        <i class="fas fa-users-slash text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">No Students Found</h3>
                                    <p class="text-sm text-gray-500 mt-1">Try adjusting your filters to find students.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $students->appends($filters)->links() }}
        </div>
    </div>
</div>
@endsection
