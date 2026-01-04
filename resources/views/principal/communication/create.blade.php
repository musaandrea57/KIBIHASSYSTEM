@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $item ? 'Edit Announcement' : 'Compose Official Message' }}</h1>
        <p class="mt-1 text-sm text-gray-600">Issue authoritative communication to institutional stakeholders.</p>
    </div>

    @include('principal.communication.partials.nav')

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ $item ? route('principal.communication.announcements.update', $item->id) : route('principal.communication.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('CONFIRMATION REQUIRED:\n\nThis action will update the official communication.\n\nAre you sure you want to proceed?');">
                @csrf
                @if($item)
                    @method('PUT')
                @endif
                
                <!-- Communication Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Communication Type <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input id="type_message" name="communication_type" type="radio" value="message" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ (old('communication_type', $type ?? 'message') == 'message' && !$item) ? 'checked' : '' }} {{ $item ? 'disabled' : '' }} onclick="toggleAnnouncementFields(false)">
                            <label for="type_message" class="ml-3 block text-sm font-medium text-gray-700 {{ $item ? 'text-gray-400' : '' }}">
                                Direct Message (Inbox)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="type_announcement" name="communication_type" type="radio" value="announcement" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ (old('communication_type', $type ?? '') == 'announcement' || $item) ? 'checked' : '' }} onclick="toggleAnnouncementFields(true)">
                            <label for="type_announcement" class="ml-3 block text-sm font-medium text-gray-700">
                                Institutional Announcement (Dashboard & Archive)
                            </label>
                        </div>
                    </div>
                    @if($item)
                        <input type="hidden" name="communication_type" value="announcement">
                    @endif
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Classification & Priority -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6 mb-6">
                    <div class="sm:col-span-3">
                        <label for="classification" class="block text-sm font-medium text-gray-700">Message Classification <span class="text-red-500">*</span></label>
                        <select id="classification" name="classification" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                            <option value="">Select Classification</option>
                            @foreach(['General Notice', 'Academic Directive', 'Examination Notice', 'Financial Notice', 'Policy Circular', 'Emergency / Urgent'] as $option)
                                <option value="{{ $option }}" {{ old('classification', $item->category ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority Level <span class="text-red-500">*</span></label>
                        <select id="priority" name="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm" required>
                            <option value="normal" {{ old('priority', $item->priority ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority', $item->priority ?? '') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $item->priority ?? '') == 'urgent' ? 'selected' : '' }}>Urgent (Dashboard Banner)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Delivery Channels <span class="text-red-500">*</span></label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="channel_system" name="channels[]" value="system" type="checkbox" checked onclick="return false;" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded bg-gray-100 disabled:opacity-50">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="channel_system" class="font-medium text-gray-700">In-System Notification (Mandatory)</label>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="channel_email" name="channels[]" value="email" type="checkbox" {{ in_array('email', old('channels', $item->channels ?? [])) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="channel_email" class="font-medium text-gray-700">Email Broadcast</label>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="channel_sms" name="channels[]" value="sms" type="checkbox" {{ in_array('sms', old('channels', $item->channels ?? [])) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="channel_sms" class="font-medium text-gray-700">SMS Alert</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Target Audience -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Target Audience</h3>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Role Selection -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Recipient Role</label>
                            <select id="role" name="filters[role]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Roles</option>
                                @foreach($filterOptions['roles'] as $role)
                                    <option value="{{ $role }}" {{ old('filters.role', $item->audience['role'] ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Year -->
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                            <select id="academic_year" name="filters[academic_year_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Years</option>
                                @foreach($filterOptions['academic_years'] as $year)
                                    <option value="{{ $year->id }}" {{ old('filters.academic_year_id', $item->audience['academic_year_id'] ?? '') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program -->
                        <div>
                            <label for="program" class="block text-sm font-medium text-gray-700">Programme</label>
                            <select id="program" name="filters[program_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Programmes</option>
                                @foreach($filterOptions['programs'] as $program)
                                    <option value="{{ $program->id }}" {{ old('filters.program_id', $item->audience['program_id'] ?? '') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Semester -->
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                            <select id="semester" name="filters[semester_id]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Semesters</option>
                                @foreach($filterOptions['semesters'] as $semester)
                                    <option value="{{ $semester->id }}" {{ old('filters.semester_id', $item->audience['semester_id'] ?? '') == $semester->id ? 'selected' : '' }}>{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- NTA Level -->
                        <div>
                            <label for="nta_level" class="block text-sm font-medium text-gray-700">NTA Level</label>
                            <select id="nta_level" name="filters[nta_level]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">All Levels</option>
                                @foreach($filterOptions['nta_levels'] as $level)
                                    <option value="{{ $level }}" {{ old('filters.nta_level', $item->audience['nta_level'] ?? '') == $level ? 'selected' : '' }}>Level {{ $level }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Specific Recipients (Optional) -->
                        <div>
                            <label for="specific_recipients" class="block text-sm font-medium text-gray-700">Specific Recipients (User IDs)</label>
                            <input type="text" name="recipients[]" id="specific_recipients" placeholder="Comma separated IDs (optional)" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to use filters above.</p>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Message Content -->
                <div class="space-y-6">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="subject" id="subject" value="{{ old('subject', $item->title ?? '') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Official subject line (concise & formal)" required>
                        </div>
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-700">Message Body <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <textarea id="body" name="body" rows="10" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-serif" placeholder="Enter official communication content here...">{{ old('body', $item->content ?? '') }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Format: Formal institutional tone. No emojis or casual slang.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attachments</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload files</span>
                                        <input id="file-upload" name="attachments[]" type="file" class="sr-only" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, DOCX, XLSX, PNG, JPG up to 10MB
                                </p>
                            </div>
                        </div>
                        @if($item && $item->attachments->count() > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700">Existing Attachments:</h4>
                                <ul class="mt-2 border border-gray-200 rounded-md divide-y divide-gray-200">
                                    @foreach($item->attachments as $attachment)
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <i class="fas fa-paperclip text-gray-400 flex-shrink-0 h-5 w-5"></i>
                                                <span class="ml-2 flex-1 w-0 truncate">{{ $attachment->filename }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="button" onclick="window.history.back()" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" name="is_published" value="0" id="btn-draft" class="ml-3 justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hidden">
                            <i class="fas fa-save mr-2"></i> Save as Draft
                        </button>
                        <button type="submit" name="is_published" value="1" id="btn-submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-paper-plane mr-2"></i> Dispatch Official Message
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#body',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    function toggleAnnouncementFields(isAnnouncement) {
        const btnDraft = document.getElementById('btn-draft');
        const btnSubmit = document.getElementById('btn-submit');
        
        if (isAnnouncement) {
            btnDraft.classList.remove('hidden');
            btnDraft.classList.add('inline-flex');
            btnSubmit.innerHTML = '<i class="fas fa-bullhorn mr-2"></i> Publish Announcement';
        } else {
            btnDraft.classList.add('hidden');
            btnDraft.classList.remove('inline-flex');
            btnSubmit.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Dispatch Official Message';
        }
    }

    // Initialize state on load
    document.addEventListener('DOMContentLoaded', function() {
        const isAnnouncement = document.getElementById('type_announcement').checked;
        toggleAnnouncementFields(isAnnouncement);
    });
</script>
@endsection
