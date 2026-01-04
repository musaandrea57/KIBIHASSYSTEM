<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use App\Models\Document;
use App\Models\AcademicYear;
use App\Http\Requests\Application\StoreStep1Request;
use App\Http\Requests\Application\StoreStep2Request;
use App\Http\Requests\Application\StoreStep3Request;
use App\Http\Requests\Application\StoreStep4Request;
use App\Http\Requests\Application\StoreStep5Request;
use App\Http\Requests\Application\StoreStep6Request;
use App\Http\Requests\Application\StoreStep7Request;
use App\Http\Requests\Application\StoreStep8Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicationController extends Controller
{
    // Map steps to view names and validation rules (conceptually)
    protected $totalSteps = 8;

    /**
     * Step 1: Account Creation (Public)
     */
    public function showStep1()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->hasRole('applicant')) {
                $application = Application::where('user_id', Auth::id())->latest()->first();
                if ($application && $application->status === 'draft') {
                    return redirect()->route('application.step', ['step' => $application->current_step]);
                }
            }
        }
        return view('application.steps.step1', ['step' => 1]);
    }

    public function storeStep1(StoreStep1Request $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone, // Ensure user table has phone or remove this
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('applicant');

            // 2. Create Draft Application
            Application::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                // store phone/email in application too for redundancy/snapshot
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'draft',
                'current_step' => 2,
                // Initialize required fields with null or defaults to avoid SQL errors if strict
                'program_id' => 1, // Placeholder, updated in Step 5
            ]);

            Auth::login($user);
        });

        return redirect()->route('application.step', ['step' => 2]);
    }

    /**
     * Generic Step Handler (Protected)
     */
    public function showStep($step)
    {
        $step = (int)$step;
        if ($step < 2 || $step > $this->totalSteps) {
            return redirect()->route('application.status');
        }

        $application = $this->getApplication();
        
        // Prevent skipping ahead
        if ($step > $application->current_step) {
            return redirect()->route('application.step', ['step' => $application->current_step]);
        }

        // Load necessary data for specific steps
        $data = compact('application', 'step');
        
        if ($step == 5) {
            $data['programs'] = Program::all(); // Assuming all programs are active if no 'active' column
            $data['academicYears'] = AcademicYear::where('is_active', true)->get();
        }

        return view('application.steps.step' . $step, $data);
    }

    public function storeStep2(StoreStep2Request $request)
    {
        $application = $this->getApplication();
        $application->update($request->validated());
        return $this->advanceStep($application, 2);
    }

    public function storeStep3(StoreStep3Request $request)
    {
        $application = $this->getApplication();
        $application->update($request->validated());
        return $this->advanceStep($application, 3);
    }

    public function storeStep4(StoreStep4Request $request)
    {
        $application = $this->getApplication();
        $application->update(['education_background' => $request->validated()]);
        return $this->advanceStep($application, 4);
    }

    public function storeStep5(StoreStep5Request $request)
    {
        $application = $this->getApplication();
        $application->update($request->validated());
        return $this->advanceStep($application, 5);
    }

    public function storeStep6(StoreStep6Request $request)
    {
        $application = $this->getApplication();
        $validated = $request->validated();
        
        $application->update([
            'nhif_card_number' => $validated['nhif_card_number'],
            'has_disability' => $request->has('has_disability'), // boolean check
            'disability_details' => $validated['disability_details'] ?? null,
            'medical_conditions' => $validated['medical_conditions'],
            'emergency_contact' => $validated['emergency_contact'],
        ]);
        return $this->advanceStep($application, 6);
    }

    public function storeStep7(StoreStep7Request $request)
    {
        $application = $this->getApplication();
        
        $types = ['birth_certificate', 'academic_certificate', 'nida_id', 'passport_photo'];
        foreach ($types as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $path = $file->store('applications/' . $application->id, 'public');
                
                // Use updateOrCreate or create new document record
                $application->uploadedDocuments()->updateOrCreate(
                    ['type' => $type],
                    [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size_kb' => round($file->getSize() / 1024),
                        'status' => 'pending'
                    ]
                );
            }
        }
        
        // Check if all required documents are present
        $requiredDocs = ['birth_certificate', 'academic_certificate', 'nida_id', 'passport_photo'];
        $uploadedTypes = $application->uploadedDocuments()->pluck('type')->toArray();
        
        $missingDocs = array_diff($requiredDocs, $uploadedTypes);
        
        if (!empty($missingDocs)) {
            return back()->withErrors(['documents' => 'Please upload all required documents: ' . implode(', ', $missingDocs)]);
        }
        
        return $this->advanceStep($application, 7);
    }

    public function storeStep8(StoreStep8Request $request)
    {
        $application = $this->getApplication();
        
        // Final Submission
        $year = date('Y');
        $count = Application::whereYear('created_at', $year)->where('status', '!=', 'draft')->count() + 1;
        $appNumber = 'KIBIHAS-APP-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
        
        $application->update([
            'declaration_accepted' => true,
            'status' => 'submitted',
            'submitted_at' => now(),
            'application_number' => $appNumber,
        ]);
        
        return redirect()->route('application.status');
    }

    private function getApplication()
    {
        return Application::where('user_id', Auth::id())->latest()->firstOrFail();
    }

    private function advanceStep(Application $application, int $currentStep)
    {
        // Only update current_step if we are progressing further than before
        if ($currentStep + 1 > $application->current_step) {
            $application->update(['current_step' => $currentStep + 1]);
        }
        
        return redirect()->route('application.step', ['step' => $currentStep + 1]);
    }

    public function track()
    {
        $application = Application::where('user_id', Auth::id())->latest()->first();
        return view('application.status', compact('application'));
    }

    public function print()
    {
        $application = $this->getApplication();
        
        // Ensure application is submitted before printing
        if ($application->status === 'draft') {
            return redirect()->route('application.status')->with('error', 'You must submit your application before printing.');
        }

        $application->load(['program', 'academicYear', 'user']);
        
        $pdf = Pdf::loadView('application.print', compact('application'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('KIBIHAS_Application_' . $application->application_number . '.pdf');
    }
}
