<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Program;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = Module::with('program');

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        $modules = $query->latest()->paginate(15);
        $programs = Program::orderBy('name')->get();

        return view('admin.academic.modules.index', compact('modules', 'programs'));
    }

    public function create()
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        return view('admin.academic.modules.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code' => 'required|string|max:20', // Unique per program check below
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
        ]);

        // Unique code per program check
        $exists = Module::where('program_id', $request->program_id)
                        ->where('code', $request->code)
                        ->exists();
        
        if ($exists) {
            return back()->withErrors(['code' => 'The module code has already been taken for this program.'])->withInput();
        }

        Module::create($validated);

        return redirect()->route('academic-setup.modules.index')->with('success', 'Module definition created successfully.');
    }

    public function edit(Module $module)
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        return view('admin.academic.modules.edit', compact('module', 'programs'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
        ]);

        // Unique code per program check (exclude current)
        $exists = Module::where('program_id', $request->program_id)
                        ->where('code', $request->code)
                        ->where('id', '!=', $module->id)
                        ->exists();
        
        if ($exists) {
            return back()->withErrors(['code' => 'The module code has already been taken for this program.'])->withInput();
        }

        $module->update($validated);

        return redirect()->route('academic-setup.modules.index')->with('success', 'Module definition updated successfully.');
    }

    public function destroy(Module $module)
    {
        try {
            $module->delete();
            return redirect()->route('academic-setup.modules.index')->with('success', 'Module definition deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete module because it has related offerings.');
        }
    }
}
