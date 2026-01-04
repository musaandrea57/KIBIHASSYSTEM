<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => User::role('teacher')->count(),
            'total_revenue' => Payment::sum('amount'),
            'recent_payments' => Payment::latest()->take(5)->get(),
        ];

        return view('principal.dashboard', compact('stats'));
    }

    public function teacherPerformance()
    {
        // Placeholder for performance report
        return view('principal.reports.teacher-performance');
    }
}
