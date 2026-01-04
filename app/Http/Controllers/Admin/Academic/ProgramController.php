<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::latest()->paginate(10);
        return view('admin.academic.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.academic.programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs',
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Program::create($validated);

        return redirect()->route('academic-setup.programs.index')->with('success', 'Program created successfully.');
    }

    public function edit(Program $program)
    {
        return view('admin.academic.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code,' . $program->id,
            'description' => 'nullable|string',
            'duration_years' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return redirect()->route('academic-setup.programs.index')->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        // Check for dependencies? Foreign key constraints will handle it or throw error.
        try {
            $program->delete();
            return redirect()->route('academic-setup.programs.index')->with('success', 'Program deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete program because it has related records.');
        }
    }
}
