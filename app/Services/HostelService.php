<?php

namespace App\Services;

use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelBed;
use App\Models\HostelAllocation;
use App\Models\HostelFeesConfig;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SemesterRegistration;
use App\Models\FeeItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\HostelBillingService;
use App\Services\AuditService;

class HostelService
{
    protected $billingService;

    public function __construct(HostelBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Get available rooms with bed counts
     */
    public function getAvailableRooms($hostelId = null, $gender = null)
    {
        $query = HostelRoom::with(['hostel', 'beds' => function($q) {
            $q->whereDoesntHave('activeAllocation');
        }])
        ->where('is_active', true);

        if ($hostelId) {
            $query->where('hostel_id', $hostelId);
        }

        if ($gender) {
            $query->whereHas('hostel', function($q) use ($gender) {
                $q->where('gender', $gender)->orWhere('gender', 'mixed');
            });
        }

        // Filter rooms that have at least one free bed
        // This is complex in SQL, easier to filter collection if dataset is small.
        // Or check count of active allocations < capacity.
        // We'll use capacity check.
        $rooms = $query->get()->filter(function($room) {
            return $room->available > 0;
        });

        return $rooms;
    }

    /**
     * Allocate a bed to a student
     */
    public function allocateBed($studentId, $roomId, $bedId, $academicYearId, $semesterId, $userId)
    {
        // 1. Check existing allocation for this semester
        $existing = HostelAllocation::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return ['success' => false, 'message' => 'Student already has an active allocation for this semester.'];
        }

        // 2. Check bed availability
        $bed = HostelBed::findOrFail($bedId);
        if ($bed->activeAllocation) {
            return ['success' => false, 'message' => 'Bed is already occupied.'];
        }
        
        // 3. Check Room gender (if needed) - assumes UI filters correctly, but backend check is good.
        $room = HostelRoom::with('hostel')->findOrFail($roomId);
        // Add gender check logic here if student gender is known.

        return DB::transaction(function () use ($studentId, $roomId, $bedId, $academicYearId, $semesterId, $userId, $room) {
            // Create Allocation
            $allocation = HostelAllocation::create([
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'hostel_id' => $room->hostel_id,
                'room_id' => $roomId,
                'bed_id' => $bedId,
                'allocated_at' => now(),
                'allocated_by' => $userId,
                'status' => 'active',
            ]);

            // Audit Log
            AuditService::log('hostel.allocated', $allocation, [], $allocation->toArray(), "Allocated bed {$bedId} to student {$studentId}");

            // Billing Integration
            $this->billingService->ensureHostelCharge(
                Student::find($studentId),
                $academicYearId,
                $semesterId,
                'Hostel Fee - ' . $room->hostel->name . ' (' . $room->room_number . ')'
            );

            return ['success' => true, 'allocation' => $allocation];
        });
    }

    /**
     * Checkout a student from hostel
     */
    public function checkout(HostelAllocation $allocation, $userId)
    {
        if ($allocation->status !== 'active') {
            return ['success' => false, 'message' => 'Allocation is not active.'];
        }

        return DB::transaction(function () use ($allocation, $userId) {
            $allocation->update([
                'status' => 'checked_out',
                'check_out_date' => now(),
            ]);

            // Audit Log
            AuditService::log('hostel.checkout', $allocation, [], $allocation->toArray(), "Student checked out by user {$userId}");

            return ['success' => true, 'message' => 'Student checked out successfully.'];
        });
    }

    /**
     * Cancel an allocation (e.g. mistake)
     */
    public function cancelAllocation(HostelAllocation $allocation, $userId)
    {
        if ($allocation->status !== 'active') {
            return ['success' => false, 'message' => 'Allocation is not active.'];
        }

        return DB::transaction(function () use ($allocation, $userId) {
            $allocation->update([
                'status' => 'cancelled',
            ]);

            // Audit Log
            AuditService::log('hostel.cancelled', $allocation, [], $allocation->toArray(), "Allocation cancelled by user {$userId}");

            // Note: We typically do NOT auto-reverse the invoice as per requirements ("reversal is handled by accountant via credit note workflow later").
            
            return ['success' => true, 'message' => 'Allocation cancelled successfully.'];
        });
    }
}
