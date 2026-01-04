@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Teacher to Module') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.assignments.store') }}">
                        @csrf

                        <!-- Module Offering -->
                        <div class="mb-4">
                            <x-input-label for="module_offering_id" :value="__('Select Module Offering (Current Year)')" />
                            <select id="module_offering_id" name="module_offering_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Module...</option>
                                @foreach($offerings as $offering)
                                    <option value="{{ $offering->id }}">
                                        {{ $offering->module->code }} - {{ $offering->module->name }} 
                                        ({{ $offering->semester->name }}, NTA {{ $offering->nta_level }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('module_offering_id')" class="mt-2" />
                            @if($offerings->isEmpty())
                                <p class="text-sm text-red-500 mt-1">No active module offerings found for the current academic year.</p>
                            @endif
                        </div>

                        <!-- Teacher -->
                        <div class="mb-4">
                            <x-input-label for="teacher_user_id" :value="__('Select Teacher')" />
                            <select id="teacher_user_id" name="teacher_user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->name }} ({{ $teacher->staffProfile->department->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('teacher_user_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Assign Teacher') }}
                            </x-primary-button>
                        </div>
                    </form>
        </div>
    </div>

@endsection
