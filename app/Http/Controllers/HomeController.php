<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Program;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('is_published', true)->orderBy('published_at', 'desc')->take(3)->get();
        $programs = Program::all();
        return view('welcome', compact('announcements', 'programs'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function campusLife()
    {
        return view('pages.campus-life');
    }

    public function admission()
    {
        return view('pages.admission');
    }

    public function ict()
    {
        return view('pages.ict-services');
    }
}
