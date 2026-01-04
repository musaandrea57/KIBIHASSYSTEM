<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FeeClearanceService;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class FeeClearedMiddleware
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Only apply to Students
        if (!$user || !$user->hasRole('student')) {
            // If parent role is added later, check here too.
            return $next($request);
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return $next($request);
        }

        // Determine Academic Year
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            // Fallback to student's current AY or latest registration
             $academicYear = $student->currentAcademicYear;
        }

        // Determine Semester
        // Try to find a way to get "Current Semester" setting.
        // Assuming we might have a config or global setting. 
        // For now, let's assume if we can't find a global "current", we use student's latest registration.
        
        $semester = null;
        
        // Strategy 1: Check if there's an active semester in the current AY
        // Assuming Semester model doesn't have 'is_current' but might have 'is_active' or similar logic
        // If not, we fallback.
        
        // Strategy 2: Student's latest registration
        if (!$semester) {
            $latestReg = $student->semesterRegistrations()
                ->where('academic_year_id', $academicYear ? $academicYear->id : null)
                ->latest()
                ->first();
                
            if ($latestReg) {
                $semester = $latestReg->semester;
                // Ensure AY matches registration if we didn't have a current AY
                if (!$academicYear) {
                    $academicYear = $latestReg->academicYear;
                }
            }
        }
        
        // Strategy 3: Student's current_semester_id field
        if (!$semester && $student->current_semester_id) {
            $semester = $student->currentSemester;
        }

        // If we still don't have context, we can't enforce clearance effectively, 
        // or maybe we should default to "Not Cleared" if context is missing?
        // Safe bet: If no registration/context found, maybe they are new or inactive.
        // Let's pass if we really can't determine context, or block?
        // Blocking is safer for "Enforcement".
        
        if (!$academicYear || !$semester) {
             // If we can't determine what they should be cleared FOR, 
             // and they are trying to access restricted content...
             // Maybe redirect to registration or error?
             // But for now, let's assume context is found.
             return $next($request);
        }

        if (!$this->feeService->isCleared($student, $academicYear, $semester)) {
            // Redirect to Fee Clearance Required page
            // We need to make sure we don't redirect loop if the page itself is protected (it shouldn't be).
            // The route for clearance page should be excluded.
            if ($request->routeIs('student.finance.clearance_required')) {
                 return $next($request);
            }
            
            return redirect()->route('student.finance.clearance_required');
        }

        return $next($request);
    }
}
