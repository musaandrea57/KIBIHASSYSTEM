<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Downloadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadableController extends Controller
{
    public function index()
    {
        $documents = Downloadable::latest()->paginate(10);
        return view('admin.downloadables.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120', // 5MB
            'category' => 'required|in:admission,announcement,general',
        ]);

        $path = $request->file('file')->store('downloads', 'public');

        Downloadable::create([
            'title' => $request->title,
            'file_path' => $path,
            'category' => $request->category,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Downloadable $downloadable)
    {
        Storage::disk('public')->delete($downloadable->file_path);
        $downloadable->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
