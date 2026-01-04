<?php

namespace App\Http\Controllers\Admin\Welfare;

use App\Http\Controllers\Controller;
use App\Models\NhifMembership;
use App\Models\NhifVerificationLog;
use App\Services\NhifService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhifController extends Controller
{
    protected $nhifService;

    public function __construct(NhifService $nhifService)
    {
        $this->nhifService = $nhifService;
        // Middleware for permissions can be applied here or in routes
        // $this->middleware('permission:manage_nhif|view_nhif');
    }

    public function index(Request $request)
    {
        if (!$request->user()->can('view_nhif') && !$request->user()->can('manage_nhif')) {
            abort(403);
        }

        $query = NhifMembership::with('student.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nhif_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $memberships = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'active' => NhifMembership::where('status', 'active')->count(),
            'expired' => NhifMembership::where('status', 'expired')->count(),
            'pending' => NhifMembership::where('status', 'pending_verification')->count(),
            'expiring_soon' => NhifMembership::where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('admin.welfare.nhif.index', compact('memberships', 'stats'));
    }

    public function verify(Request $request, NhifMembership $membership)
    {
        if (!$request->user()->can('manage_nhif')) {
            abort(403);
        }

        $result = $this->nhifService->verifyMembership($membership->nhif_number, Auth::id());

        if ($result['found']) {
            return back()->with('success', 'NHIF Verified: ' . ucfirst($result['status']));
        } else {
            return back()->with('error', 'NHIF Verification Failed: Not Found or Error.');
        }
    }

    public function update(Request $request, NhifMembership $membership)
    {
        if (!$request->user()->can('manage_nhif')) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,expired,pending_verification',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $membership->update([
            'status' => $validated['status'],
            'expiry_date' => $validated['expiry_date'],
            'notes' => $validated['notes'],
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'source' => 'manual', // Overridden manually
        ]);

        // Log manual update
        NhifVerificationLog::create([
            'nhif_membership_id' => $membership->id,
            'checked_at' => now(),
            'result_status' => $validated['status'],
            'response_payload' => ['manual_override' => true, 'notes' => $validated['notes']],
            'checked_by' => Auth::id(),
        ]);

        return back()->with('success', 'NHIF Membership updated manually.');
    }
}
