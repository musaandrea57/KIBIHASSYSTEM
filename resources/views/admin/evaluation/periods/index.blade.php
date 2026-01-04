@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Evaluation Periods</h2>
                    <a href="{{ route('admin.evaluation.periods.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Schedule Period</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluations</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($periods as $period)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $period->academicYear->year ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $period->semester->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $period->start_date }}</div>
                                <div class="text-sm text-gray-500">to {{ $period->end_date }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $period->evaluations_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $period->is_open ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $period->is_open ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.evaluation.periods.edit', $period) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                
                                <form action="{{ route('admin.evaluation.periods.generate', $period) }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="period_id" value="{{ $period->id }}">
                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Generate Evaluations</button>
                                </form>

                                <form action="{{ route('admin.evaluation.periods.destroy', $period) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will delete the period only if no submissions exist.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
