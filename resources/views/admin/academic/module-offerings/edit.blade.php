@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Module Offering') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('academic-setup.module-offerings.update', $moduleOffering) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="module_id" class="block text-sm font-medium text-gray-700">Module</label>
                                <select name="module_id" id="module_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Select Module</option>
                                    @foreach($programs as $prog)
                                        <optgroup label="{{ $prog->name }}">
                                            @foreach($prog->modules as $module)
                                                <option value="{{ $module->id }}" {{ old('module_id', $moduleOffering->module_id) == $module->id ? 'selected' : '' }}>
                                                    {{ $module->code }} - {{ $module->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('module_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                                <select name="academic_year_id" id="academic_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $moduleOffering->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="semester_id" class="block text-sm font-medium text-gray-700">Semester</label>
                                <select name="semester_id" id="semester_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ old('semester_id', $moduleOffering->semester_id) == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                                    @endforeach
                                </select>
                                @error('semester_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nta_level" class="block text-sm font-medium text-gray-700">NTA Level</label>
                                <select name="nta_level" id="nta_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="4" {{ old('nta_level', $moduleOffering->nta_level) == 4 ? 'selected' : '' }}>NTA Level 4</option>
                                    <option value="5" {{ old('nta_level', $moduleOffering->nta_level) == 5 ? 'selected' : '' }}>NTA Level 5</option>
                                    <option value="6" {{ old('nta_level', $moduleOffering->nta_level) == 6 ? 'selected' : '' }}>NTA Level 6</option>
                                </select>
                                @error('nta_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="active" {{ old('status', $moduleOffering->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $moduleOffering->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('academic-setup.module-offerings.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2 hover:bg-gray-300">Cancel</a>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update Offering</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
