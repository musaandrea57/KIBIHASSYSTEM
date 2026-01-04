@extends('layouts.portal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">My Profile</h1>
        <p class="text-sm text-slate-500 mt-1">Manage your account settings and profile information.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center text-center">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg mb-4 bg-gray-100">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500 text-4xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Photo Upload Trigger (Visual only, form is below) -->
                    <label for="photo_upload" class="absolute bottom-4 right-0 bg-blue-600 text-white p-2 rounded-full shadow-md cursor-pointer hover:bg-blue-700 transition-colors" title="Change Photo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </label>
                </div>

                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-2">
                    Principal
                </span>
                
                <div class="mt-6 w-full space-y-3">
                    <div class="flex items-center text-sm text-slate-600">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $user->email }}
                    </div>
                    @if($user->username)
                    <div class="flex items-center text-sm text-slate-600">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 2-2 2H4"></path></svg>
                        ID: {{ $user->username }}
                    </div>
                    @endif
                    <div class="flex items-center text-sm text-slate-600">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Joined {{ $user->created_at->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-6 pb-2 border-b border-gray-100">Edit Profile Details</h3>

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('principal.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Hidden file input linked to the camera icon -->
                    <input type="file" id="photo_upload" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('photo-preview-name').textContent = this.files[0].name">

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full bg-slate-50 border border-slate-200 text-slate-500 rounded-lg px-4 py-2 cursor-not-allowed">
                            <p class="text-xs text-slate-400 mt-1">Name changes must be requested through HR.</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div id="photo-preview-section" class="pt-2">
                            <span class="text-sm text-slate-500">Selected Photo: </span>
                            <span id="photo-preview-name" class="text-sm font-medium text-slate-800">None</span>
                            <p class="text-xs text-slate-400 mt-1">Click the camera icon on the profile card to select a new photo.</p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-medium">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Read-Only Institutional Info -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6">
                <h3 class="font-bold text-slate-800 mb-4">Institutional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Department</label>
                        <p class="text-slate-800 font-medium">{{ $user->staffProfile->department->name ?? 'Administration' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Role</label>
                        <p class="text-slate-800 font-medium">Principal / Chief Executive</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">System Access Level</label>
                        <p class="text-slate-800 font-medium">Executive (Read-Only Operational)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Last Login</label>
                        <p class="text-slate-800 font-medium">{{ now()->format('M d, Y H:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
