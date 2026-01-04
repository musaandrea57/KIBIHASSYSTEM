@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Schedule Evaluation Period</h2>
                    <a href="{{ route('admin.evaluation.periods.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Back</a>
                </div>

                <form method="POST" action="{{ route('admin.evaluation.periods.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Academic Year</label>
                            <select name="academic_year_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Semester</label>
                            <select name="semester_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                            <input type="datetime-local" name="start_date" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">End Date</label>
                            <input type="datetime-local" name="end_date" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Period</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
