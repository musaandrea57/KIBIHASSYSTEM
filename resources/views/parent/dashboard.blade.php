@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent Dashboard') }}
        </h2></div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">My Children</h3>
                    
                    @if($children->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($children as $child)
                                <div class="bg-gray-50 rounded-lg shadow border border-gray-200 p-6 flex flex-col items-center text-center">
                                    <div class="w-24 h-24 mb-4">
                                        @if($child->profile_photo_path)
                                            <img src="{{ Storage::url($child->profile_photo_path) }}" alt="{{ $child->user->name }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm">
                                        @else
                                            <div class="w-full h-full rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-2xl font-bold border-4 border-white shadow-sm">
                                                {{ substr($child->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <h4 class="font-bold text-lg text-gray-900">{{ $child->user->name }}</h4>
                                    <p class="text-sm text-gray-500 mb-2">{{ $child->registration_number }}</p>
                                    
                                    <div class="text-left w-full mt-4 space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Program:</span>
                                            <span class="font-medium text-gray-900">{{ $child->program->code }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Year:</span>
                                            <span class="font-medium text-gray-900">{{ $child->currentAcademicYear->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Status:</span>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold 
                                                {{ $child->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($child->status) }}
                                            </span>

                                    <a href="{{ route('parent.child.details', $child) }}" class="mt-6 w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded transition">
                                        View Details & Results
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No children linked to your account.</p>
                            <p class="text-sm text-gray-400 mt-2">Please contact the administration to link your student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
