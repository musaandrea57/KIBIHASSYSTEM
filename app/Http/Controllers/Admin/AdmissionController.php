<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Semester;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['program', 'user']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('program_id', $request->program_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.admissions.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load(['uploadedDocuments', 'program', 'user']);
        return view('admin.admissions.show', compact('application'));
    }

    public function approve(Request $request, Application $application)
    {
        // Allow approval if submitted, under_review, OR correction_required (if they fixed it via other means or admin decides to override)
        if (!in_array($application->status, ['submitted', 'under_review', 'correction_required'])) {
            return back()->with('error', 'Application cannot be approved in its current status (' . $application->status . ').');
        }

        DB::beginTransaction();
        try {
            // 1. Update Application Status
            $application->update(['status' => 'approved']);

            // 2. Get Active Academic Year
            $academicYear = AcademicYear::where('is_active', true)->first();
            if (!$academicYear) {
                // Fallback to latest if no active
                $academicYear = AcademicYear::latest()->first();
            }

            // 3. Get Semester 1
            $semester = Semester::where('name', 'LIKE', '%Semester 1%')
                ->orWhere('number', 1)
                ->first();
            
            if (!$semester) {
                 $semester = Semester::first();
            }

            // 4. Generate Registration Number (KIB/YYYY/XXXX)
            $year = date('Y');
            
            // Safer generation strategy: Get last student of this year to determine sequence
            $lastStudent = Student::where('registration_number', 'LIKE', "KIB/$year/%")
                                ->orderBy('id', 'desc')
                                ->first();
            
            $sequence = 1;
            if ($lastStudent) {
                // Extract number from KIB/2025/0001
                $parts = explode('/', $lastStudent->registration_number);
                if (count($parts) >= 3) {
                    $sequence = intval(end($parts)) + 1;
                }
            }
            
            $regNumber = 'KIB/' . $year . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Double check uniqueness just in case
            while (Student::where('registration_number', $regNumber)->exists()) {
                $sequence++;
                $regNumber = 'KIB/' . $year . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // 5. Create Student Record with Full Biodata
            $student = Student::create([
                'user_id' => $application->user_id,
                'registration_number' => $regNumber,
                'program_id' => $application->program_id,
                'current_nta_level' => 4, // Default entry level
                'current_academic_year_id' => $academicYear->id,
                'current_semester_id' => $semester ? $semester->id : null,
                'status' => 'active',
                
                // Basic Info
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'last_name' => $application->last_name,
                'gender' => $application->gender,
                'date_of_birth' => $application->dob,
                'phone' => $application->phone,
                
                // Extended Biodata
                'nationality' => $application->nationality,
                'nin' => $application->nin,
                'passport_number' => $application->passport_number,
                'marital_status' => $application->marital_status,
                'permanent_address' => $application->permanent_address,
                'current_address' => $application->current_address,
                'region' => $application->region,
                'country' => $application->country,
                'nhif_card_number' => $application->nhif_card_number,
                'has_disability' => $application->has_disability,
                'disability_details' => $application->disability_details,
                'medical_conditions' => $application->medical_conditions,
            ]);

            // 6. Update User Role
            $user = $application->user;
            if ($user) {
                $user->assignRole('student');
                // Optionally remove applicant role, but keeping it as history is fine
            }
            
            // 7. Associate Documents with Student (Copy or Re-link?)
            // For audit, we keep documents on Application. 
            // If Student needs them, we can link or query via Application.
            // But documents table is polymorphic. We can duplicate the records pointing to Student?
            // Or just leave them on Application. Usually student records need to access these docs.
            // Let's create new document records for the student pointing to the same file path.
            
            foreach ($application->uploadedDocuments as $doc) {
                $student->uploadedDocuments()->create([
                    'type' => $doc->type,
                    'path' => $doc->path,
                    'original_name' => $doc->original_name,
                    'mime_type' => $doc->mime_type,
                    'size_kb' => $doc->size_kb,
                    'status' => 'verified', // Auto-verify on admission
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
            }

            // 8. Generate Invoice from Fee Structure
            $feeStructure = FeeStructure::where('program_id', $student->program_id)
                ->where('academic_year_id', $student->current_academic_year_id)
                ->where('semester_id', $student->current_semester_id)
                ->where('nta_level', $student->current_nta_level)
                ->where('status', 'active')
                ->with('items.feeItem')
                ->first();

            if ($feeStructure) {
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'program_id' => $student->program_id,
                    'nta_level' => $student->current_nta_level,
                    'academic_year_id' => $student->current_academic_year_id,
                    'semester_id' => $student->current_semester_id,
                    'fee_structure_id' => $feeStructure->id,
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
                    'issue_date' => now(),
                    'due_date' => now()->addDays(30),
                    'status' => 'unpaid',
                    'subtotal' => 0,
                    'total_paid' => 0,
                    'balance' => 0,
                    'created_by' => Auth::id(),
                ]);

                foreach ($feeStructure->items as $item) {
                    $invoice->items()->create([
                        'fee_item_id' => $item->fee_item_id,
                        'description' => $item->feeItem ? $item->feeItem->name : 'Fee Item',
                        'amount' => $item->total_amount,
                        'paid_amount' => 0,
                    ]);
                }

                $invoice->recalculateTotals();
            }

            DB::commit();

            return redirect()->route('admin.admissions.show', $application)->with('success', 'Application approved and student record created. Reg No: ' . $regNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return back()->with('error', 'Error approving application: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Application $application)
    {
        Log::info("Admin " . Auth::id() . " rejected application {$application->id}");
        $application->update(['status' => 'rejected']);
        return back()->with('success', 'Application rejected.');
    }
    
    public function requestCorrection(Request $request, Application $application)
    {
        $request->validate([
            'admin_feedback' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => 'correction_required',
            'admin_feedback' => $request->admin_feedback,
        ]);

        // Send email notification (To be implemented)

        return back()->with('success', 'Application sent back for correction.');
    }
}
