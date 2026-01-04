@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold">{{ $template->name }}</h2>
                        <p class="text-gray-500">{{ $template->description }}</p>
                    </div>
                    <button onclick="document.getElementById('addQuestionModal').showModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Question</button>
                </div>

                <div class="space-y-4">
                    @foreach($template->questions as $question)
                    <div class="border p-4 rounded bg-gray-50 flex justify-between items-center">
                        <div>
                            <span class="font-bold mr-2">{{ $question->sort_order }}.</span>
                            <span>{{ $question->question_text }}</span>
                            <span class="ml-2 text-xs text-gray-500">({{ $question->question_type }})</span>
                            @if($question->is_required)
                            <span class="ml-2 text-xs text-red-500">*Required</span>
                            @endif
                        </div>
                        <div>
                            <!-- Edit/Delete could go here -->
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<dialog id="addQuestionModal" class="p-6 rounded shadow-lg w-1/3">
    <form method="POST" action="{{ route('admin.evaluation.templates.questions.store', $template) }}">
        @csrf
        <h3 class="text-lg font-bold mb-4">Add Question</h3>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Question Text</label>
            <textarea name="question_text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Type</label>
            <select name="question_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="likert_1_5">Likert Scale (1-5)</option>
                <option value="text">Text Comment</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Sort Order</label>
            <input type="number" name="sort_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $template->questions->count() + 1 }}">
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_required" class="form-checkbox" checked>
                <span class="ml-2">Required</span>
            </label>
        </div>
        <div class="flex justify-end">
            <button type="button" onclick="document.getElementById('addQuestionModal').close()" class="mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add</button>
        </div>
    </form>
</dialog>
@endsection
