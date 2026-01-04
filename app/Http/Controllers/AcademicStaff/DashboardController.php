<?php

namespace App\Http\Controllers\AcademicStaff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Module;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'programs' => Program::count(),
            'modules' => Module::count(),
        ];

        return view('academic_staff.dashboard', compact('stats'));
    }
}
