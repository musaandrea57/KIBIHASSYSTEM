<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->paginate(10);
        return view('admin.academic.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20', // e.g. 2025/2026
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // If is_current is passed (though we handle it separately mostly), but let's stick to separate action.
        
        AcademicYear::create($validated);

        return redirect()->route('academic-setup.academic-years.index')->with('success', 'Academic Year created successfully.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        AcademicYear::query()->update(['is_current' => false]);
        $academicYear->update(['is_current' => true, 'is_active' => true]); // Ensure it's active too

        return back()->with('success', 'Current academic year updated to ' . $academicYear->name);
    }
}
