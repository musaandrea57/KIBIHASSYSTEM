<x-public-layout>
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-primary-900 py-6 px-8">
                <h1 class="text-2xl font-bold text-white">Online Application Form</h1>
                <p class="text-primary-200 text-sm mt-1">Academic Year {{ date('Y') }}/{{ date('Y') + 1 }}</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-8 mb-0">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Please check the form for errors.
                            </p>
                            <ul class="list-disc list-inside text-xs text-red-600 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf

                <!-- Program Choice -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Program Selection</h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="program_id" class="block text-sm font-medium text-gray-700">Select Program</label>
                            <select id="program_id" name="program_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">-- Choose a Diploma Program --</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }} ({{ $program->code }}) - NTA Level {{ $program->nta_level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Personal Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" name="dob" id="dob" value="{{ old('dob') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select name="gender" id="gender" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">-- Select --</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                            <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Tanzanian') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+255..." required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Account Setup -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Account Setup</h2>
                    <p class="text-sm text-gray-500 mb-4">Create your login credentials to track your application status.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Education Background -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Education Background (O-Level)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="index_number" class="block text-sm font-medium text-gray-700">Index Number</label>
                            <input type="text" name="index_number" id="index_number" value="{{ old('index_number') }}" placeholder="S0000/0000/YYYY" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="school_name" class="block text-sm font-medium text-gray-700">School Name</label>
                            <input type="text" name="school_name" id="school_name" value="{{ old('school_name') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="completion_year" class="block text-sm font-medium text-gray-700">Completion Year</label>
                            <input type="number" name="completion_year" id="completion_year" value="{{ old('completion_year') }}" min="2000" max="{{ date('Y') }}" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Documents Upload -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Required Documents</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="passport_photo" class="block text-sm font-medium text-gray-700">Passport Size Photo</label>
                            <p class="text-xs text-gray-500 mb-1">Image file (JPG/PNG), max 2MB</p>
                            <input type="file" name="passport_photo" id="passport_photo" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <div>
                            <label for="csee_certificate" class="block text-sm font-medium text-gray-700">CSEE Certificate (Form 4)</label>
                            <p class="text-xs text-gray-500 mb-1">PDF or Image, max 5MB</p>
                            <input type="file" name="csee_certificate" id="csee_certificate" accept=".pdf,image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <div class="md:col-span-2">
                            <label for="birth_certificate" class="block text-sm font-medium text-gray-700">Birth Certificate</label>
                            <p class="text-xs text-gray-500 mb-1">PDF or Image, max 5MB</p>
                            <input type="file" name="birth_certificate" id="birth_certificate" accept=".pdf,image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm bg-secondary text-primary-900 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 font-bold text-lg">
                        Submit Application
                    </button>
                    <p class="text-center text-sm text-gray-500 mt-4">
                        Already applied? <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">Log in here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
</x-public-layout>
