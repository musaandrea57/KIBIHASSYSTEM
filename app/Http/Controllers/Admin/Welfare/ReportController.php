<?php

namespace App\Http\Controllers\Admin\Welfare;

use App\Http\Controllers\Controller;
use App\Models\NhifMembership;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function nhif(Request $request)
    {
        // Filters
        $status = $request->query('status');
        
        $query = NhifMembership::with(['student.user', 'student.program']);

        if ($status == 'expired') {
            $query->where('status', 'expired');
        } elseif ($status == 'expiring_soon') {
            $query->where('expiry_date', '<', now()->addDays(30))
                  ->where('expiry_date', '>', now());
        } elseif ($status == 'pending') {
            $query->where('status', 'pending_verification');
        }

        $memberships = $query->paginate(20);

        return view('admin.welfare.reports.nhif', compact('memberships'));
    }

    public function hostel(Request $request)
    {
        // 1. Occupancy by Hostel
        $occupancy = Hostel::withCount(['rooms', 'beds'])
            ->get()
            ->map(function ($hostel) {
                $allocatedBeds = DB::table('hostel_allocations')
                    ->where('hostel_id', $hostel->id)
                    ->where('status', 'active')
                    ->count();
                
                $totalBeds = $hostel->beds_count;
                
                return [
                    'name' => $hostel->name,
                    'total_beds' => $totalBeds,
                    'occupied' => $allocatedBeds,
                    'available' => $totalBeds - $allocatedBeds,
                    'occupancy_rate' => $totalBeds > 0 ? round(($allocatedBeds / $totalBeds) * 100, 1) : 0
                ];
            });

        // 2. Allocations List
        $allocations = HostelAllocation::with(['student.user', 'hostel', 'room', 'bed', 'academicYear', 'semester'])
            ->latest()
            ->paginate(20);

        return view('admin.welfare.reports.hostel', compact('occupancy', 'allocations'));
    }
}
