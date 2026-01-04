<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleOffering;
use App\Models\ModuleResult;
use App\Models\OfficialResultUpload;
use App\Models\Program;
use App\Services\ResultsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    protected $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function index()
    {
        // List module offerings with pending results or all offerings
        $offerings = ModuleOffering::with(['module'])
            ->withCount(['moduleResults as pending_count' => function ($q) {
                $q->where('status', 'pending_admin_approval');
            }])
            ->get();

        return view('admin.results.index', compact('offerings'));
    }

    public function show(ModuleOffering $offering)
    {
        $results = ModuleResult::where('module_offering_id', $offering->id)
            ->with('student')
            ->orderBy('student_id') // Sort by student
            ->get();

        return view('admin.results.show', compact('offering', 'results'));
    }

    public function publish(ModuleOffering $offering)
    {
        $this->resultsService->publishResultsForOffering($offering, Auth::user());
        return back()->with('success', 'Results published successfully.');
    }

    // Official Result Uploads
    public function uploadOfficial()
    {
        $uploads = OfficialResultUpload::with(['program', 'academicYear', 'semester'])->latest()->get();
        return view('admin.results.upload', compact('uploads'));
    }

    public function storeOfficial(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240',
            'program_id' => 'required|exists:programs,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'nta_level' => 'required|integer',
        ]);

        $path = $request->file('file')->store('official_results', 'public');

        OfficialResultUpload::create([
            'program_id' => $request->program_id,
            'academic_year_id' => $request->academic_year_id,
            'semester_id' => $request->semester_id,
            'nta_level' => $request->nta_level,
            'file_path' => $path,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'uploaded_by' => Auth::id(),
            'status' => 'pending_admin_approval',
        ]);

        return back()->with('success', 'Official results uploaded for approval.');
    }

    public function approveOfficial(OfficialResultUpload $upload)
    {
        $upload->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'published_at' => now(), // Auto publish
        ]);
        return back()->with('success', 'Official results approved and published.');
    }
}
