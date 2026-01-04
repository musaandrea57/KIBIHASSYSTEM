<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Hostel;
use App\Models\HostelBlock;
use App\Models\HostelRoom;
use App\Models\HostelBed;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\HostelFeesConfig;
use App\Models\Student;
use App\Models\User;
use App\Models\NhifMembership;
use App\Models\HostelAllocation;
use App\Services\HostelService;
use App\Services\HostelBillingService;
use App\Models\FeeItem;

class Module7Seeder extends Seeder
{
    public function run()
    {
        // 1. Create Permissions
        $permissions = [
            'manage_nhif',
            'view_nhif',
            'manage_hostels',
            'allocate_hostels',
            'view_hostels',
            'generate_hostel_invoices',
            'view_welfare_reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Assign to Roles
        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo($permissions);

        // Welfare (Create if not exists)
        $welfare = Role::firstOrCreate(['name' => 'welfare']);
        $welfare->givePermissionTo([
            'manage_nhif',
            'manage_hostels',
            'allocate_hostels',
            'view_welfare_reports',
            'view_nhif',
            'view_hostels',
            // 'generate_hostel_invoices' - optionally given, let's include it if they need to bill
             'generate_hostel_invoices'
        ]);

        // Academic (view only for NHIF/Hostels usually, maybe just NHIF)
        $academic = Role::firstOrCreate(['name' => 'academic']);
        $academic->givePermissionTo(['view_nhif']);

        // Principal (Read only)
        $principal = Role::firstOrCreate(['name' => 'principal']);
        $principal->givePermissionTo(['view_nhif', 'view_hostels', 'view_welfare_reports']);


        // 3. Seed Hostels Data
        $hostelA = Hostel::firstOrCreate([
            'code' => 'H001',
        ], [
            'name' => 'Kilimanjaro Hall',
            'gender' => 'mixed',
            'location' => 'Main Campus',
            'is_active' => true,
        ]);

        // Blocks
        $blockA = HostelBlock::firstOrCreate([
            'hostel_id' => $hostelA->id,
            'name' => 'Block A (Male)',
        ], [
            'code' => 'BLK-A',
            'is_active' => true,
        ]);

        $blockB = HostelBlock::firstOrCreate([
            'hostel_id' => $hostelA->id,
            'name' => 'Block B (Female)',
        ], [
            'code' => 'BLK-B',
            'is_active' => true,
        ]);

        // Rooms & Beds
        // Block A Rooms (101-105)
        for ($i = 1; $i <= 5; $i++) {
            $room = HostelRoom::firstOrCreate([
                'hostel_id' => $hostelA->id,
                'room_number' => 'A10' . $i,
            ], [
                'block_id' => $blockA->id,
                'room_type' => 'dorm',
                'capacity' => 4,
                'is_active' => true,
            ]);

            // Create Beds
            foreach (['A', 'B', 'C', 'D'] as $label) {
                HostelBed::firstOrCreate([
                    'room_id' => $room->id,
                    'bed_label' => $label,
                ], [
                    'is_active' => true,
                ]);
            }
        }

        // Block B Rooms (101-105)
        for ($i = 1; $i <= 5; $i++) {
            $room = HostelRoom::firstOrCreate([
                'hostel_id' => $hostelA->id,
                'room_number' => 'B10' . $i,
            ], [
                'block_id' => $blockB->id,
                'room_type' => 'dorm',
                'capacity' => 4,
                'is_active' => true,
            ]);

            foreach (['A', 'B', 'C', 'D'] as $label) {
                HostelBed::firstOrCreate([
                    'room_id' => $room->id,
                    'bed_label' => $label,
                ], [
                    'is_active' => true,
                ]);
            }
        }
        
        // 4. Seed Hostel Fee Config & Fee Item
        $currentYear = AcademicYear::where('is_current', true)->first();
        $semester = Semester::where('is_active', true)->first();
        
        // Ensure Fee Item exists (Integration with Module 4)
        $feeItem = FeeItem::firstOrCreate([
            'name' => 'Hostel Fee',
        ], [
            // 'code' => 'HST-FEE', // Removed in new schema
            // 'amount' => 150000.00, // Removed in new schema
            // 'is_tuition' => false, // Removed in new schema
            'default_description' => 'Accommodation Fee',
        ]);

        if ($currentYear && $semester) {
            HostelFeesConfig::firstOrCreate([
                'academic_year_id' => $currentYear->id,
                'semester_id' => $semester->id,
                'program_id' => null, // Global fee
            ], [
                'amount' => 150000.00, // 150k TZS
                'is_mandatory' => true,
                'fee_item_id' => $feeItem->id,
            ]);
        }

        // 5. Create Sample Allocations & NHIF
        // Find a student
        $student = Student::first();
        $adminUser = User::role('admin')->first() ?? User::first();

        if ($student && $currentYear && $semester) {
            // A. NHIF
            NhifMembership::firstOrCreate([
                'student_id' => $student->id,
            ], [
                'nhif_number' => '123456789012',
                'membership_type' => 'student',
                'status' => 'pending_verification',
                'scheme_name' => 'Toto Afya',
                'issued_date' => now()->subMonths(6),
                'expiry_date' => now()->addMonths(6),
                'source' => 'manual',
            ]);

            // B. Hostel Allocation
            // Check if already allocated
            $existingAlloc = HostelAllocation::where('student_id', $student->id)
                ->where('academic_year_id', $currentYear->id)
                ->where('semester_id', $semester->id)
                ->first();
                
            if (!$existingAlloc) {
                // Find a bed
                $bed = HostelBed::where('is_active', true)
                    ->whereDoesntHave('activeAllocation')
                    ->first();

                if ($bed) {
                    $billingService = new HostelBillingService();
                    $hostelService = new HostelService($billingService);

                    $hostelService->allocateBed(
                        $student->id,
                        $bed->room_id,
                        $bed->id,
                        $currentYear->id,
                        $semester->id,
                        $adminUser->id
                    );
                    
                    $this->command->info("Allocated bed {$bed->bed_label} to student {$student->admission_number}");
                }
            }
        }
    }
}

