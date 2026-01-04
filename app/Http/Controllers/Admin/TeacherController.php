<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('teacher')->with(['staffProfile.department']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('staffProfile', function ($q) use ($search) {
                      $q->where('staff_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('staffProfile', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $teachers = $query->paginate(10);
        $departments = Department::where('is_active', true)->get();

        return view('admin.teachers.index', compact('teachers', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.teachers.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'staff_id' => ['required', 'string', 'unique:staff_profiles'],
            'department_id' => ['required', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:M,F'],
            'employed_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('teacher');

            StaffProfile::create([
                'user_id' => $user->id,
                'staff_id' => $request->staff_id,
                'department_id' => $request->department_id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'employed_at' => $request->employed_at,
                'status' => 'active',
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher account created successfully.');
    }

    public function edit(User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }
        $teacher->load('staffProfile');
        $departments = Department::where('is_active', true)->get();
        return view('admin.teachers.edit', compact('teacher', 'departments'));
    }

    public function update(Request $request, User $teacher)
    {
        if (!$teacher->hasRole('teacher')) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'staff_id' => ['required', 'string', 'unique:staff_profiles,staff_id,' . $teacher->staffProfile->id],
            'department_id' => ['required', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:M,F'],
            'employed_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($request, $teacher) {
            $teacher->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $teacher->staffProfile->update([
                'staff_id' => $request->staff_id,
                'department_id' => $request->department_id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'employed_at' => $request->employed_at,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher profile updated successfully.');
    }
}
