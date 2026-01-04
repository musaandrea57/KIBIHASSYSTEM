<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\NhifMembership;
use App\Services\NhifService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhifController extends Controller
{
    protected $nhifService;

    public function __construct(NhifService $nhifService)
    {
        $this->nhifService = $nhifService;
    }

    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        $membership = NhifMembership::where('student_id', $student->id)->first();

        return view('student.nhif.index', compact('membership'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        $request->validate([
            'nhif_number' => 'required|unique:nhif_memberships,nhif_number',
        ]);

        $validation = $this->nhifService->validateNumberFormat($request->nhif_number);
        if (!$validation['valid']) {
            return back()->withErrors(['nhif_number' => $validation['message']]);
        }

        NhifMembership::create([
            'student_id' => $student->id,
            'nhif_number' => $request->nhif_number,
            'status' => 'pending_verification',
            'membership_type' => 'student',
        ]);

        return back()->with('success', 'NHIF Number submitted for verification.');
    }
}
