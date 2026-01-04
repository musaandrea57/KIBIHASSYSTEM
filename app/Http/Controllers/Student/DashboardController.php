<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseRegistration;
use App\Models\Module;
use App\Models\Student;
use App\Models\Result;
use App\Models\NhifMembership;
use App\Services\FeeClearanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    protected $feeService;

    public function __construct(FeeClearanceService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            // Should handle case where user has 'student' role but no record (rare if flow is correct)
            return redirect()->route('home')->with('error', 'Student record not found.');
        }

        $activeYear = $student->currentAcademicYear;
        $activeSemester = $student->currentSemester;

        // Get registrations for current session
        $registrations = CourseRegistration::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('semester_id', $activeSemester->id)
            ->with('module')
            ->get();

        // Check if registered
        $isRegistered = $registrations->count() > 0;

        // Available modules for registration (if not registered)
        $availableModules = [];
        if (!$isRegistered && $activeSemester && $activeYear) {
            $availableModules = Module::where('program_id', $student->program_id)
                ->whereHas('offerings', function ($query) use ($student, $activeSemester, $activeYear) {
                    $query->where('nta_level', $student->current_nta_level)
                          ->where('semester_id', $activeSemester->id)
                          ->where('academic_year_id', $activeYear->id);
                })
                ->get();
        }
        
        // Get recent results
        $recentResults = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->latest()
            ->take(5)
            ->with('module')
            ->get();

        // Check fee clearance status
        $isCleared = $this->feeService->isCleared($student, $activeYear, $activeSemester);

        // Get NHIF Membership
        $nhifMembership = NhifMembership::where('student_id', $student->id)->first();

        return view('student.dashboard', compact('student', 'activeYear', 'activeSemester', 'registrations', 'isRegistered', 'availableModules', 'recentResults', 'isCleared', 'nhifMembership'));
    }

    public function results()
    {
        $user = Auth::user();
        $student = $user->student;
        
        $results = Result::where('student_id', $student->id)
            ->where('is_published', true)
            ->with(['module', 'academicYear', 'semester'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('semester_id', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->academicYear->year . ' - ' . $item->semester->name;
            });

        return view('student.results', compact('student', 'results'));
    }

    public function continuousAssessment()
    {
        return view('student.continuous_assessment');
    }

    public function transcript()
    {
        return view('student.transcript');
    }

    public function officialResults()
    {
        return view('student.official_results');
    }

    public function registerCourses(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $user = Auth::user();
        $student = $user->student;
        $activeYear = $student->currentAcademicYear;
        $activeSemester = $student->currentSemester;

        // Validation: Credits
        $selectedModules = Module::whereIn('id', $request->modules)->get();
        $totalCredits = $selectedModules->sum('credits');
        $minCredits = $student->program->min_credits_per_semester ?? 15;
        $maxCredits = $student->program->max_credits_per_semester ?? 30;

        if ($totalCredits < $minCredits || $totalCredits > $maxCredits) {
            return back()->with('error', "Credit load validation failed. You selected $totalCredits credits. Allowed range: $minCredits - $maxCredits credits.");
        }

        foreach ($request->modules as $moduleId) {
            CourseRegistration::create([
                'student_id' => $student->id,
                'module_id' => $moduleId,
                'academic_year_id' => $activeYear->id,
                'semester_id' => $activeSemester->id,
                'status' => 'registered',
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Courses registered successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // 2MB Max
        ]);

        $user = Auth::user();
        $student = $user->student;

        if ($request->file('photo')) {
            // Delete old photo if exists
            if ($student->profile_photo_path) {
                Storage::disk('public')->delete($student->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $student->update(['profile_photo_path' => $path]);
        }

        return back()->with('success', 'Profile photo updated.');
    }
}
