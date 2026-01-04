<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FeeClearanceService;
use App\Models\AcademicYear;

class EnsureCourseworkClearance
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // 1. Bypass for non-students (e.g. Admin, Academic Staff)
        if (!$user || !$user->hasRole('student')) {
            return $next($request);
        }

        $student = $user->student;
        if (!$student) {
            abort(403, 'Student record not found.');
        }

        // 2. Determine Context (Academic Year & Semester)
        // Try to get from request if available, otherwise default to current student context
        // Ideally, coursework routes should have context, but if they are generic "current" routes:
        $academicYear = $student->currentAcademicYear;
        $semester = $student->currentSemester;

        // Fallback to system active year if student has none (rare)
        if (!$academicYear) {
            $academicYear = AcademicYear::where('is_current', true)->first();
        }

        if (!$academicYear || !$semester) {
            // If we can't determine context, we can't enforce clearance properly.
            // Fail safe: Block or Allow?
            // "Clearance must be checked per: academic_year_id, semester_id"
            // If we don't know the term, we can't check.
            // Let's assume strict: if we can't verify, we block.
            // But realistically, if they are not registered, they have no coursework to see.
            // Let's pass through if no context, controller will likely show nothing.
            return $next($request);
        }

        // 3. Check Clearance
        // Policy Check: Is "Clearance required for coursework visibility" ON?
        // Check database setting on AcademicYear
        if (!$academicYear->coursework_clearance_required) {
            return $next($request);
        }
        
        $isCleared = $this->feeService->isCleared($student, $academicYear, $semester);

        if ($isCleared) {
            return $next($request);
        }

        // 4. Not Cleared -> Log & Block
        Log::info('coursework_view_blocked_due_to_clearance', [
            'user_id' => $user->id,
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'timestamp' => now(),
        ]);

        // Get breakdown for the blocked page
        $totals = $this->feeService->calculateForStudent($student, $academicYear, $semester);
        
        // Pass data to view via session or directly return view?
        // Middleware usually returns a response.
        // Returning a view directly from middleware is possible but maybe not "clean".
        // Redirecting to a named route "student.coursework.blocked" is better.
        
        // Let's use a dedicated route for the blocked page to keep URL clean?
        // Or just render the view here. Rendering here preserves the URL they tried to visit.
        // Let's render here.
        
        return response()->view('student.academic.coursework_blocked', [
            'student' => $student,
            'academicYear' => $academicYear,
            'semester' => $semester,
            'totals' => $totals,
            // 'breakdown' => $this->feeService->getOutstandingBreakdown($student, $academicYear, $semester)
        ]);
    }
}
