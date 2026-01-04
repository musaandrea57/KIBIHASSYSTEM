<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\ModuleOffering;
use App\Models\Module;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Program;
use Illuminate\Http\Request;

class ModuleOfferingController extends Controller
{
    public function index(Request $request)
    {
        $query = ModuleOffering::with(['module.program', 'semester', 'academicYear']);

        if ($request->program_id) {
            $query->whereHas('module', function($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }
        if ($request->academic_year_id) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->semester_id) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->nta_level) {
            $query->where('nta_level', $request->nta_level);
        }

        $offerings = $query->latest()->paginate(15);

        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = Semester::orderBy('number')->get();

        return view('admin.academic.module-offerings.index', compact('offerings', 'programs', 'academicYears', 'semesters'));
    }

    public function create()
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $semesters = Semester::where('is_active', true)->orderBy('number')->get();
        
        return view('admin.academic.module-offerings.create', compact('programs', 'academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'nta_level' => 'required|in:4,5,6',
            'status' => 'required|in:active,inactive',
        ]);

        // Unique constraint check
        $exists = ModuleOffering::where('module_id', $request->module_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('semester_id', $request->semester_id)
            ->where('nta_level', $request->nta_level)
            ->exists();

        if ($exists) {
            return back()->withErrors(['module_id' => 'This offering already exists.'])->withInput();
        }

        ModuleOffering::create($validated);

        return redirect()->route('academic-setup.module-offerings.index')->with('success', 'Module Offering created successfully.');
    }

    public function edit(ModuleOffering $moduleOffering)
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $semesters = Semester::where('is_active', true)->orderBy('number')->get();
        
        return view('admin.academic.module-offerings.edit', compact('moduleOffering', 'programs', 'academicYears', 'semesters'));
    }

    public function update(Request $request, ModuleOffering $moduleOffering)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'nta_level' => 'required|in:4,5,6',
            'status' => 'required|in:active,inactive',
        ]);

        // Unique check excluding current
        $exists = ModuleOffering::where('module_id', $request->module_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('semester_id', $request->semester_id)
            ->where('nta_level', $request->nta_level)
            ->where('id', '!=', $moduleOffering->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['module_id' => 'This offering already exists.'])->withInput();
        }

        $moduleOffering->update($validated);

        return redirect()->route('academic-setup.module-offerings.index')->with('success', 'Module Offering updated successfully.');
    }

    public function destroy(ModuleOffering $moduleOffering)
    {
        $moduleOffering->delete();
        return redirect()->route('academic-setup.module-offerings.index')->with('success', 'Module Offering deleted successfully.');
    }
}
