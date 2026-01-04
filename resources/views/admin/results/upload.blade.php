@extends('layouts.portal')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Upload Official Results</h2>
        <p class="text-gray-600">Upload signed result sheets (PDF) for archiving and Ministry compliance</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4">New Upload</h3>
            <form action="{{ route('admin.results.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Program</label>
                    <select name="program_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        @foreach(\App\Models\Program::all() as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">NTA Level</label>
                    <select name="nta_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="4">NTA Level 4</option>
                        <option value="5">NTA Level 5</option>
                        <option value="6">NTA Level 6</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <select name="academic_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        @foreach(\App\Models\AcademicYear::all() as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Semester</label>
                    <select name="semester_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        @foreach(\App\Models\Semester::all() as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">PDF File</label>
                    <input type="file" name="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100" required>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Uploads List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg mb-4">Recent Uploads</h3>
            @if($uploads->isEmpty())
                <p class="text-gray-500">No uploads yet.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($uploads as $upload)
                    <li class="py-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $upload->original_filename }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $upload->program->code }} - Level {{ $upload->nta_level }} - {{ $upload->academicYear->name }}
                                </p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $upload->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $upload->status)) }}
                                </span>
                            </div>
                            @if($upload->status === 'pending_admin_approval')
                            <form action="{{ route('admin.results.upload.approve', $upload->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">Approve</button>
                            </form>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

@endsection
