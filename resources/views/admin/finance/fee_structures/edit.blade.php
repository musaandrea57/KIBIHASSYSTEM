@extends('layouts.portal')

@section('content')
<div x-data="feeStructureBuilder()" class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-4">
            <li>
                <div class="flex items-center">
                    <a href="{{ route('admin.finance.fee-structures.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Fee Structures</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <a href="{{ route('admin.finance.fee-structures.show', $feeStructure) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Details</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Edit Fee Structure</h2>
            <p class="mt-1 text-sm text-gray-500">
                @if($feeStructure->status !== 'draft')
                    <span class="text-yellow-600 font-medium">Note: Editing this {{ $feeStructure->status }} structure will create a new draft version.</span>
                @else
                    Update fee items, installments, and accounts.
                @endif
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <button type="button" @click="save('draft')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Draft
            </button>
            <button type="button" @click="save('published')" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Publish
            </button>
        </div>
    </div>

    <form id="fee-structure-form" action="{{ route('admin.finance.fee-structures.update', $feeStructure) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="form-status" value="draft">

        <!-- Header Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Tuition -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tuition</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="formatMoney(totals.tuition)"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Other Costs -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Other Costs</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="formatMoney(totals.other)"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grand Total -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Grand Total</dt>
                                <dd class="text-2xl font-bold text-indigo-600" x-text="formatMoney(totals.grand)"></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installment Summary -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Installments</h3>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="block text-gray-400">Oct</span>
                            <span class="block font-medium text-gray-900" x-text="formatMoney(totals.oct)"></span>
                        </div>
                        <div>
                            <span class="block text-gray-400">Jan</span>
                            <span class="block font-medium text-gray-900" x-text="formatMoney(totals.jan)"></span>
                        </div>
                        <div>
                            <span class="block text-gray-400">Apr</span>
                            <span class="block font-medium text-gray-900" x-text="formatMoney(totals.apr)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white shadow sm:rounded-lg mb-6 overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Context</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Structure Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $feeStructure->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Nursing NTA4 Sem1 2025/2026">
                </div>
                <div>
                    <label for="program_id" class="block text-sm font-medium text-gray-700">Programme</label>
                    <select name="program_id" id="program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Programme</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ $feeStructure->program_id == $program->id ? 'selected' : '' }}>{{ $program->name }} ({{ $program->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nta_level" class="block text-sm font-medium text-gray-700">NTA Level</label>
                    <select name="nta_level" id="nta_level" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Level</option>
                        <option value="4" {{ $feeStructure->nta_level == 4 ? 'selected' : '' }}>NTA Level 4</option>
                        <option value="5" {{ $feeStructure->nta_level == 5 ? 'selected' : '' }}>NTA Level 5</option>
                        <option value="6" {{ $feeStructure->nta_level == 6 ? 'selected' : '' }}>NTA Level 6</option>
                    </select>
                </div>
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <select name="academic_year_id" id="academic_year_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Year</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $feeStructure->academic_year_id == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="semester_id" class="block text-sm font-medium text-gray-700">Semester</label>
                    <select name="semester_id" id="semester_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ $feeStructure->semester_id == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tuition Fees Section -->
        <div class="bg-white shadow sm:rounded-lg mb-6 overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tuition Fees</h3>
                <button type="button" @click="addTuitionItem()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Add Tuition Item
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fee Item</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Oct</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Apr</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr x-show="item.category === 'tuition'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select :name="'items['+index+'][fee_item_id]'" x-model="item.fee_item_id" class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select Fee Item</option>
                                        @foreach($feeItems as $fi)
                                            @if($fi->category == 'tuition' || str_contains(strtolower($fi->name), 'tuition'))
                                            <option value="{{ $fi->id }}">{{ $fi->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_oct]'" x-model.number="item.amount_oct" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_jan]'" x-model.number="item.amount_jan" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_apr]'" x-model.number="item.amount_apr" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    <span x-text="formatMoney(item.amount_oct + item.amount_jan + item.amount_apr)"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select :name="'items['+index+'][bank_account_id]'" x-model="item.bank_account_id" class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Default</option>
                                        @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="hidden" :name="'items['+index+'][is_mandatory]'" :value="item.is_mandatory ? 1 : 0">
                                    <input type="checkbox" x-model="item.is_mandatory" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!items.some(i => i.category === 'tuition')">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No tuition fee items added.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Costs Section -->
        <div class="bg-white shadow sm:rounded-lg mb-6 overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Other Costs / Institute Fees</h3>
                <button type="button" @click="addOtherItem()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    + Add Other Item
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fee Item</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Oct</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Apr</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr x-show="item.category === 'other'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select :name="'items['+index+'][fee_item_id]'" x-model="item.fee_item_id" class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select Fee Item</option>
                                        @foreach($feeItems as $fi)
                                            @if($fi->category != 'tuition' && !str_contains(strtolower($fi->name), 'tuition'))
                                            <option value="{{ $fi->id }}">{{ $fi->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_oct]'" x-model.number="item.amount_oct" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_jan]'" x-model.number="item.amount_jan" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="'items['+index+'][amount_apr]'" x-model.number="item.amount_apr" min="0" step="100" class="block w-full text-right text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    <span x-text="formatMoney(item.amount_oct + item.amount_jan + item.amount_apr)"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select :name="'items['+index+'][bank_account_id]'" x-model="item.bank_account_id" class="block w-full text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Default</option>
                                        @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="hidden" :name="'items['+index+'][is_mandatory]'" :value="item.is_mandatory ? 1 : 0">
                                    <input type="checkbox" x-model="item.is_mandatory" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!items.some(i => i.category === 'other')">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No other fee items added.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Change Log Panel -->
        @if(isset($auditLogs) && $auditLogs->count() > 0)
        <div class="bg-white shadow sm:rounded-lg mb-6 overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Change Log</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">History of changes for this fee structure.</p>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($auditLogs as $log)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-indigo-600 truncate">
                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                        </div>
                        <div class="ml-2 flex-shrink-0 flex">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 sm:flex sm:justify-between">
                        <div class="sm:flex">
                            <p class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ $log->user->name ?? 'System' }}
                            </p>
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                            @if($log->payload)
                                <span class="truncate max-w-xs" title="{{ json_encode($log->payload) }}">
                                    {{ \Illuminate\Support\Str::limit(json_encode($log->payload), 50) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </form>
</div>

<script>
    function feeStructureBuilder() {
        return {
            items: [],
            
            get totals() {
                let oct = 0, jan = 0, apr = 0, tuition = 0, other = 0;
                
                this.items.forEach(item => {
                    let itemTotal = (item.amount_oct || 0) + (item.amount_jan || 0) + (item.amount_apr || 0);
                    oct += (item.amount_oct || 0);
                    jan += (item.amount_jan || 0);
                    apr += (item.amount_apr || 0);
                    
                    if (item.category === 'tuition') {
                        tuition += itemTotal;
                    } else {
                        other += itemTotal;
                    }
                });

                return {
                    oct: oct,
                    jan: jan,
                    apr: apr,
                    tuition: tuition,
                    other: other,
                    grand: tuition + other
                };
            },

            addTuitionItem() {
                this.items.push({
                    id: Date.now(),
                    category: 'tuition',
                    fee_item_id: '',
                    amount_oct: 0,
                    amount_jan: 0,
                    amount_apr: 0,
                    bank_account_id: '',
                    is_mandatory: true
                });
            },

            addOtherItem() {
                this.items.push({
                    id: Date.now(),
                    category: 'other',
                    fee_item_id: '',
                    amount_oct: 0,
                    amount_jan: 0,
                    amount_apr: 0,
                    bank_account_id: '',
                    is_mandatory: true
                });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            save(status) {
                document.getElementById('form-status').value = status;
                // Validate
                if (this.items.length === 0) {
                    alert('Please add at least one fee item.');
                    return;
                }
                document.getElementById('fee-structure-form').submit();
            },

            formatMoney(amount) {
                return new Intl.NumberFormat('en-TZ', { style: 'decimal', minimumFractionDigits: 2 }).format(amount);
            },
            
            init() {
                // Initialize items from server data
                const serverItems = @json($feeStructure->items);
                
                this.items = serverItems.map(item => {
                    let category = 'other';
                    if (item.fee_item) {
                        if (item.fee_item.category === 'tuition' || item.fee_item.name.toLowerCase().includes('tuition')) {
                            category = 'tuition';
                        }
                    }
                    
                    return {
                        id: item.id,
                        category: category,
                        fee_item_id: item.fee_item_id,
                        amount_oct: parseFloat(item.amount_oct),
                        amount_jan: parseFloat(item.amount_jan),
                        amount_apr: parseFloat(item.amount_apr),
                        bank_account_id: item.bank_account_id,
                        is_mandatory: item.is_mandatory
                    };
                });
            }
        }
    }
</script>
@endsection
