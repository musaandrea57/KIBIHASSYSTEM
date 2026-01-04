@extends('layouts.portal')

@section('content')
    <div class="mb-6"><h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programme Level Rules') }}
        </h2></div>

    @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Create Rule -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Set/Update Rule</h3>
                    <form method="POST" action="{{ route('admin.registrations.rules.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div class="col-span-2">
                                <x-input-label for="program_id" :value="__('Program')" />
                                <select id="program_id" name="program_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->code }} - {{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="nta_level" :value="__('NTA Level')" />
                                <select id="nta_level" name="nta_level" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    @foreach([4, 5, 6, 7, 8] as $level)
                                        <option value="{{ $level }}">Level {{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="min_credits" :value="__('Min Credits')" />
                                <x-text-input id="min_credits" class="block mt-1 w-full" type="number" name="min_credits" :value="30" required />
                            </div>
                            <div>
                                <x-input-label for="max_credits" :value="__('Max Credits')" />
                                <x-text-input id="max_credits" class="block mt-1 w-full" type="number" name="max_credits" :value="45" required />
                         <div class="mt-4">
                            <x-primary-button>Save Rule</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Rules -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Existing Rules</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Credits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Credits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rules as $rule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $rule->program->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $rule->nta_level }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $rule->min_credits }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $rule->max_credits }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $rule->updated_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection