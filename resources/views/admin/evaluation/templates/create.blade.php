@extends('layouts.portal')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Create Evaluation Template</h2>
                    <a href="{{ route('admin.evaluation.templates.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Back</a>
                </div>

                <form method="POST" action="{{ route('admin.evaluation.templates.store') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Template Name</label>
                        <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-blue-600" checked>
                            <span class="ml-2 text-gray-700">Active</span>
                        </label>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-4">Questions</h3>
                        <div id="questions-container">
                            <!-- JS will populate this -->
                        </div>
                        <button type="button" onclick="addQuestion()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">+ Add Question</button>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let questionCount = 0;

function addQuestion() {
    const container = document.getElementById('questions-container');
    const index = questionCount++;
    
    const div = document.createElement('div');
    div.className = "mb-4 p-4 border rounded bg-gray-50";
    div.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-bold text-gray-600">Question ${index + 1}</h4>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">Remove</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Question Text</label>
                <input type="text" name="questions[${index}][text]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="questions[${index}][type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="likert">Likert Scale (1-5)</option>
                    <option value="text">Text Comment</option>
                </select>
            </div>
        </div>
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="questions[${index}][required]" class="form-checkbox text-indigo-600" checked>
                <span class="ml-2 text-gray-700">Required</span>
            </label>
        </div>
    `;
    container.appendChild(div);
}

// Add one question by default
window.onload = function() {
    addQuestion();
};
</script>
@endsection
