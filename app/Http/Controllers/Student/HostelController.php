<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HostelAllocation;
use Illuminate\Support\Facades\Auth;

class HostelController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        $allocations = HostelAllocation::with(['hostel', 'room', 'bed', 'academicYear', 'semester'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view('student.hostel.index', compact('allocations'));
    }
}
