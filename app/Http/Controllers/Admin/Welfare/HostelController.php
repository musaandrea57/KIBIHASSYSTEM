<?php

namespace App\Http\Controllers\Admin\Welfare;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelAllocation;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\HostelBed;
use App\Services\HostelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HostelController extends Controller
{
    protected $hostelService;

    public function __construct(HostelService $hostelService)
    {
        $this->hostelService = $hostelService;
    }

    // Hostels CRUD
    public function index(Request $request)
    {
        if (!$request->user()->can('view_hostels')) abort(403);
        $hostels = Hostel::withCount('rooms')->get();
        return view('admin.welfare.hostels.index', compact('hostels'));
    }

    public function show(Request $request, Hostel $hostel)
    {
        if (!$request->user()->can('view_hostels')) abort(403);
        $hostel->load(['blocks', 'rooms.beds.activeAllocation']);
        return view('admin.welfare.hostels.show', compact('hostel'));
    }

    // Allocations Management
    public function allocations(Request $request)
    {
        if (!$request->user()->can('view_hostels')) abort(403);
        
        $query = HostelAllocation::with(['student', 'hostel', 'room', 'bed', 'academicYear', 'semester'])
            ->where('status', 'active');

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        $allocations = $query->latest()->paginate(20);
        $hostels = Hostel::all();

        return view('admin.welfare.hostels.allocations', compact('allocations', 'hostels'));
    }

    public function createAllocation(Request $request)
    {
        if (!$request->user()->can('allocate_hostels')) abort(403);
        
        $hostels = Hostel::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $semesters = Semester::all();
        
        return view('admin.welfare.hostels.allocate', compact('hostels', 'academicYears', 'semesters'));
    }

    public function getAvailableBeds(Request $request)
    {
        $request->validate(['hostel_id' => 'required']);
        
        $rooms = $this->hostelService->getAvailableRooms($request->hostel_id);
        
        // Transform for AJAX
        $data = $rooms->map(function($room) {
            return [
                'id' => $room->id,
                'number' => $room->room_number . " (" . $room->room_type . ")",
                'available_beds' => $room->beds->filter(fn($b) => !$b->activeAllocation)->values()->map(fn($b) => ['id' => $b->id, 'label' => $b->bed_label])
            ];
        });

        return response()->json($data);
    }
    
    public function searchStudent(Request $request)
    {
        $term = $request->term;
        $students = Student::where('first_name', 'like', "%$term%")
            ->orWhere('last_name', 'like', "%$term%")
            ->orWhere('admission_number', 'like', "%$term%")
            ->take(10)
            ->get()
            ->map(function($s) {
                return ['id' => $s->id, 'text' => $s->first_name . ' ' . $s->last_name . ' (' . $s->admission_number . ')'];
            });
        return response()->json($students);
    }

    public function storeAllocation(Request $request)
    {
        if (!$request->user()->can('allocate_hostels')) abort(403);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'bed_id' => 'required|exists:hostel_beds,id',
        ]);

        $result = $this->hostelService->allocateBed(
            $request->student_id,
            $request->room_id,
            $request->bed_id,
            $request->academic_year_id,
            $request->semester_id,
            Auth::id()
        );

        if ($result['success']) {
            return redirect()->route('admin.welfare.hostels.allocations')->with('success', 'Bed allocated successfully.');
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function checkout(Request $request, HostelAllocation $allocation)
    {
        if (!$request->user()->can('manage_hostels')) abort(403);
        
        $result = $this->hostelService->checkout($allocation, Auth::id());
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }

    public function cancel(Request $request, HostelAllocation $allocation)
    {
        if (!$request->user()->can('manage_hostels')) abort(403);
        
        $result = $this->hostelService->cancelAllocation($allocation, Auth::id());
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }
}
