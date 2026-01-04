@extends('layouts.applicant')

@section('content')
<div class="mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-2xl font-bold text-gray-900">Create Your Account</h2>
    <p class="mt-1 text-sm text-gray-600">Start your application by creating an account. You can save your progress and return later.</p>
</div>

<form method="POST" action="{{ route('application.register.store') }}">
    @csrf

    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <div class="mt-1">
                <input type="text" name="first_name" id="first_name" autocomplete="given-name" required value="{{ old('first_name') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('first_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <div class="mt-1">
                <input type="text" name="last_name" id="last_name" autocomplete="family-name" required value="{{ old('last_name') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('last_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="other_names" class="block text-sm font-medium text-gray-700">Middle/Other Names (Optional)</label>
            <div class="mt-1">
                <input type="text" name="other_names" id="other_names" autocomplete="additional-name" value="{{ old('other_names') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('other_names') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <div class="mt-1">
                <input type="tel" name="phone" id="phone" autocomplete="tel" required value="{{ old('phone') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <div class="mt-1">
                <input type="email" name="email" id="email" autocomplete="email" required value="{{ old('email') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <p class="mt-1 text-sm text-gray-500">We'll use this for all official communication.</p>
            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
                <input type="password" name="password" id="password" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="mt-1">
                <input type="password" name="password_confirmation" id="password_confirmation" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
        </div>
    </div>

    <div class="mt-8 pt-5 border-t border-gray-200">
        <div class="flex justify-end">
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Account & Start Application
            </button>
        </div>
    </div>
</form>
@endsection
