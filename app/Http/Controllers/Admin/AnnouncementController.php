<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        Announcement::create([
            'title' => $request->title,
            'summary' => $request->summary,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $announcement->update([
            'title' => $request->title,
            'summary' => $request->summary,
            'content' => $request->content,
            'published_at' => $request->published_at,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully.');
    }
}
