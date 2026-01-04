<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ApplicationController extends Controller
{
    public function create()
    {
        $programs = Program::all();
        return view('application.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Personal
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|string|in:Male,Female',
            'nationality' => 'required|string',
            
            // Account
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Program
            'program_id' => 'required|exists:programs,id',

            // Education
            'index_number' => 'required|string',
            'school_name' => 'required|string',
            'completion_year' => 'required|integer|min:2000|max:' . (date('Y')),

            // Documents
            'passport_photo' => 'required|image|max:2048', // 2MB
            'csee_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'birth_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // 1. Create User
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('applicant');

        // 2. Handle File Uploads
        $documents = [];
        if ($request->hasFile('passport_photo')) {
            $path = $request->file('passport_photo')->store('applications/photos', 'public');
            $documents['passport_photo'] = $path;
        }
        if ($request->hasFile('csee_certificate')) {
            $path = $request->file('csee_certificate')->store('applications/certificates', 'public');
            $documents['csee_certificate'] = $path;
        }
        if ($request->hasFile('birth_certificate')) {
            $path = $request->file('birth_certificate')->store('applications/certificates', 'public');
            $documents['birth_certificate'] = $path;
        }

        // 3. Generate Application Number
        $year = date('Y');
        $count = Application::whereYear('created_at', $year)->count() + 1;
        $appNumber = 'KIBIHAS-APP-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);

        // 4. Create Application
        $application = Application::create([
            'application_number' => $appNumber,
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'program_id' => $request->program_id,
            'status' => 'submitted',
            'biodata' => [
                'dob' => $request->dob,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'address' => $request->address ?? '',
            ],
            'education_background' => [
                'index_number' => $request->index_number,
                'school_name' => $request->school_name,
                'completion_year' => $request->completion_year,
            ],
            'documents' => $documents,
        ]);

        // 5. Login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Application submitted successfully! Your Application Number is ' . $appNumber);
    }

    public function track()
    {
        $user = Auth::user();
        $application = Application::where('user_id', $user->id)->latest()->first();
        
        if (!$application) {
            return redirect()->route('application.create');
        }

        return view('application.status', compact('application'));
    }

    public function showTracking(Request $request)
    {
        $request->validate([
            'application_number' => 'required|exists:applications,application_number',
        ]);
        
        $application = Application::where('application_number', $request->application_number)->first();
        
        return view('application.status', compact('application'));
    }
}
