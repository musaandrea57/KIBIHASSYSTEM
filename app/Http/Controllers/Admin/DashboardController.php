<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Application;
use App\Models\Program;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => User::role('teacher')->count(),
            'departments' => Department::count(),
            'applications' => Application::where('status', 'submitted')->count(),
            'programs' => Program::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
