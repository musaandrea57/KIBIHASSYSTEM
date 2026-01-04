<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['program', 'user']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('program_id', $request->program_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.admissions.index', compact('applications'));
    }

    public function show(Application $application)
    {
        return view('admin.admissions.show', compact('application'));
    }

    public function approve(Request $request, Application $application)
    {
        if ($application->status != 'submitted' && $application->status != 'under_review') {
            return back()->with('error', 'Application cannot be approved in its current status.');
        }

        DB::beginTransaction();
        try {
            // 1. Update Application Status
            $application->update(['status' => 'approved']);

            // 2. Get Active Academic Year
            $academicYear = AcademicYear::where('is_active', true)->first();
            if (!$academicYear) {
                // Fallback to latest if no active
                $academicYear = AcademicYear::latest()->first();
            }

            // 3. Get Semester 1
            $semester = Semester::where('academic_year_id', $academicYear->id)
                ->where('name', 'LIKE', '%Semester 1%')
                ->first();
            
            if (!$semester) {
                 $semester = Semester::where('academic_year_id', $academicYear->id)->first();
            }

            // 4. Generate Registration Number (KIB/YYYY/XXXX)
            $year = date('Y');
            $count = Student::whereYear('created_at', $year)->count() + 1;
            $regNumber = 'KIB/' . $year . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // 5. Create Student Record
            $student = Student::create([
                'user_id' => $application->user_id,
                'registration_number' => $regNumber,
                'program_id' => $application->program_id,
                'current_nta_level' => 4, // Default entry level
                'current_academic_year_id' => $academicYear->id,
                'current_semester_id' => $semester ? $semester->id : null,
                'status' => 'active',
            ]);

            // 6. Update User Role
            $user = $application->user;
            $user->assignRole('student');
            // Optionally remove applicant role, but keeping it as history is fine
            
            DB::commit();

            return redirect()->route('admin.admissions.show', $application)->with('success', 'Application approved and student record created. Reg No: ' . $regNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Error approving application: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Application $application)
    {
        $application->update(['status' => 'rejected']);
        return back()->with('success', 'Application rejected.');
    }
}
