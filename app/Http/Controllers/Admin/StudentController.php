<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'program', 'currentAcademicYear', 'currentSemester']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('registration_number', 'like', "%{$search}%");
        }

        $students = $query->latest()->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load(['user', 'program', 'currentAcademicYear', 'currentSemester', 'guardians.user']);
        return view('admin.students.show', compact('student'));
    }

    public function createGuardian(Student $student)
    {
        return view('admin.students.invite-parent', compact('student'));
    }

    public function storeGuardian(Request $request, Student $student)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'relationship' => 'required|string|in:Father,Mother,Guardian',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        $password = null;
        $isNewUser = false;

        if (!$user) {
            $password = Str::random(8);
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($password),
            ]);
            $isNewUser = true;
        }

        // Assign parent role
        if (!$user->hasRole('parent')) {
            $user->assignRole('parent');
        }

        // Link to student
        $exists = StudentGuardian::where('student_id', $student->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            StudentGuardian::create([
                'student_id' => $student->id,
                'user_id' => $user->id,
                'relationship' => $request->relationship,
            ]);
        }

        $message = 'Parent linked successfully.';
        if ($isNewUser) {
            $message .= " New account created. Password: $password";
        }

        return redirect()->route('admin.students.show', $student)->with('success', $message);
    }
}
