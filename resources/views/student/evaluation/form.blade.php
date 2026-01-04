@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6 border-b pb-4">
                    <h2 class="text-2xl font-semibold">{{ $evaluation->moduleAssignment->module->name }}</h2>
                    <p class="text-gray-600">Lecturer: {{ $evaluation->teacher->name }}</p>
                    <p class="text-sm text-gray-500 mt-2">Evaluation Period: {{ $evaluation->period->academicYear->name }} - {{ $evaluation->period->semester->name }}</p>
                </div>

                <form method="POST" action="{{ route('student.evaluation.store', $evaluation) }}">
                    @csrf
                    
                    <div class="space-y-8">
                        @foreach($template->questions as $question)
                        <div class="p-4 bg-gray-50 rounded">
                            <label class="block font-medium text-gray-800 mb-3">
                                {{ $question->sort_order }}. {{ $question->question_text }}
                                @if($question->is_required) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($question->question_type === 'likert_1_5')
                                <div class="flex justify-between max-w-md mx-auto">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="flex flex-col items-center cursor-pointer">
                                        <input type="radio" name="answers[{{ $question->id }}][score]" value="{{ $i }}" class="mb-1" {{ $question->is_required ? 'required' : '' }}>
                                        <span class="text-xs text-gray-600">{{ $i }}</span>
                                    </label>
                                    @endfor
                                </div>
                                <div class="flex justify-between max-w-md mx-auto mt-1 text-xs text-gray-400">
                                    <span>Poor</span>
                                    <span>Excellent</span>
                                </div>
                            @elseif($question->question_type === 'text')
                                <textarea name="answers[{{ $question->id }}][text]" class="w-full border rounded p-2" rows="3" {{ $question->is_required ? 'required' : '' }}></textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('student.evaluation.index') }}" class="mr-4 px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" onclick="return confirm('Are you sure? You cannot edit this evaluation after submission.')">Submit Evaluation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
