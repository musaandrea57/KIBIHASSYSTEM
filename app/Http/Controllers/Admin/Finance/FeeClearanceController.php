<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeClearanceOverride;
use App\Models\FeeClearanceStatus;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeClearanceController extends Controller
{
    public function index(Request $request)
    {
        // Filters
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');
        $programId = $request->get('program_id');
        $ntaLevel = $request->get('nta_level');
        $status = $request->get('status');

        $query = FeeClearanceStatus::with(['student.user', 'student.program', 'academicYear', 'semester']);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($programId) {
            $query->whereHas('student', function($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        
        if ($ntaLevel) {
            $query->whereHas('student', function($q) use ($ntaLevel) {
                $q->where('current_nta_level', $ntaLevel);
            });
        }

        $clearanceStatuses = $query->latest('updated_at')->paginate(20);

        // Filter options
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $semesters = Semester::all();
        $programs = Program::all();

        return view('admin.finance.clearance.index', compact('clearanceStatuses', 'academicYears', 'semesters', 'programs'));
    }

    public function storeOverride(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'reason' => 'required|string|max:255',
            'expires_at' => 'required|date|after:today',
        ]);

        DB::transaction(function () use ($request) {
            // Deactivate any existing active overrides for this context
            FeeClearanceOverride::where('student_id', $request->student_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('semester_id', $request->semester_id)
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->update([
                    'revoked_at' => now(),
                    'revoked_by' => Auth::id(),
                    'revocation_reason' => 'New override granted',
                ]);

            // Create new override
            FeeClearanceOverride::create([
                'student_id' => $request->student_id,
                'academic_year_id' => $request->academic_year_id,
                'semester_id' => $request->semester_id,
                'reason' => $request->reason,
                'expires_at' => $request->expires_at,
                'granted_at' => now(),
                'granted_by' => Auth::id(),
            ]);

            // Update status
            FeeClearanceStatus::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                ],
                [
                    'status' => 'overridden',
                    'calculated_at' => now(),
                ]
            );
        });

        return back()->with('success', 'Fee clearance override granted successfully.');
    }

    public function revokeOverride(Request $request, $id)
    {
        $override = FeeClearanceOverride::findOrFail($id);

        $override->update([
            'revoked_at' => now(),
            'revoked_by' => Auth::id(),
            'revocation_reason' => 'Manually revoked by admin',
        ]);

        // Trigger a refresh of the status via service (or just set to not_cleared for now, but better to recalculate)
        // Since we are in controller, let's just trigger a recalculation via the service if we can inject it, 
        // or just manually update the status to 'not_cleared' and let the next calculation fix it.
        // Actually, let's instantiate the service to be clean.
        
        $feeService = app(\App\Services\FeeClearanceService::class);
        $feeService->refreshSnapshot($override->student, $override->academicYear, $override->semester);

        return back()->with('success', 'Override revoked successfully.');
    }
}
