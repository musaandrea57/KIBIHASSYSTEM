@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Evaluation') }}
        </h2></div>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold">{{ $evaluation->moduleOffering->module->name }} ({{ $evaluation->moduleOffering->module->code }})</h3>
                        <p class="text-gray-600">Lecturer: <span class="font-semibold">{{ $evaluation->teacher->name }}</span></p>
                        <p class="text-sm text-gray-500 mt-2">
                            Please provide honest feedback. Your responses are anonymous to the lecturer.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.evaluation.store', $evaluation->id) }}">
                        @csrf

                        @foreach($template->questions as $question)
                            <div class="mb-6">
                                <label class="block text-gray-700 font-bold mb-2">
                                    {{ $question->order }}. {{ $question->question_text }}
                                    @if($question->is_required) <span class="text-red-500">*</span> @endif
                                </label>

                                @if($question->type === 'likert')
                                    <div class="flex items-center space-x-6">
                                        @foreach(range(1, 5) as $rating)
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="q_{{ $question->id }}" value="{{ $rating }}" class="mr-2" {{ old("q_{$question->id}") == $rating ? 'checked' : '' }}>
                                                <span>{{ $rating }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex justify-between w-64">
                                        <span>Poor</span>
                                        <span>Excellent</span>
                                    </div>
                                @elseif($question->type === 'text')
                                    <textarea name="q_{{ $question->id }}" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old("q_{$question->id}") }}</textarea>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex justify-end pt-4 border-t">
                            <a href="{{ route('student.evaluation.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded mr-2 hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold" onclick="return confirm('Are you sure you want to submit? You cannot edit this evaluation later.')">
                                Submit Evaluation
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
