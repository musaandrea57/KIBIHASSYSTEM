<?php

use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DownloadableController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\Academic\ProgramController;
use App\Http\Controllers\Admin\Academic\AcademicYearController;
use App\Http\Controllers\Admin\Academic\SemesterController;
use App\Http\Controllers\Admin\Academic\ModuleController;
use App\Http\Controllers\Admin\Academic\ModuleOfferingController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ModuleAssignmentController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\AcademicStaff\DashboardController as AcademicStaffDashboardController;
use App\Http\Controllers\Principal\DashboardController as PrincipalDashboardController;
use App\Http\Controllers\Principal\TeacherPerformanceController;
use App\Http\Controllers\Principal\AcademicReportsController;
use App\Http\Controllers\Principal\StudentPerformanceController;
use App\Http\Controllers\Student\RegistrationController as StudentRegistrationController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/campus-life', [HomeController::class, 'campusLife'])->name('campus-life');
Route::get('/admission', [HomeController::class, 'admission'])->name('admission');
Route::get('/ict-services', [HomeController::class, 'ict'])->name('ict-services');

// Role-based Dashboard Redirect
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('student')) {
        return redirect()->route('student.dashboard');
    } elseif ($user->hasRole('applicant')) {
        return redirect()->route('application.status');
    } elseif ($user->hasRole('parent')) {
        return redirect()->route('parent.dashboard');
    } elseif ($user->hasRole('principal')) {
        return redirect()->route('principal.dashboard');
    } elseif ($user->hasRole('teacher')) {
        return redirect()->route('teacher.dashboard');
    } elseif ($user->hasRole('academic_staff')) {
        return redirect()->route('academic_staff.dashboard');
    } elseif ($user->hasRole('accountant')) {
        return redirect()->route('accountant.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Welfare Reports
    Route::middleware('permission:view_welfare_reports')->group(function() {
        Route::get('reports/nhif', [\App\Http\Controllers\Admin\Welfare\ReportController::class, 'nhif'])->name('reports.nhif');
        Route::get('reports/hostels', [\App\Http\Controllers\Admin\Welfare\ReportController::class, 'hostel'])->name('reports.hostel');
    });
});

// Attachment Routes
Route::middleware('auth')->group(function () {
    Route::get('attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
});

// Message Routes
Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('index');
    Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
    Route::get('/create', [MessageController::class, 'create'])->name('create');
    Route::post('/', [MessageController::class, 'store'])->name('store');
    Route::get('/{message}', [MessageController::class, 'show'])->name('show');
    Route::post('/{message}/acknowledge', [MessageController::class, 'acknowledge'])->name('acknowledge');
});

// Application Routes (Public Step 1)
Route::get('/application/register', [ApplicationController::class, 'showStep1'])->name('application.register');
Route::post('/application/register', [ApplicationController::class, 'storeStep1'])->name('application.register.store');

// Application Routes (Protected Steps 2-8)
    Route::middleware(['auth', 'role:applicant'])->prefix('application')->name('application.')->group(function () {
        Route::get('/status', [ApplicationController::class, 'track'])->name('status');
        Route::get('/print', [ApplicationController::class, 'print'])->name('print');
        
        // Explicit POST routes for each step to allow Form Request injection
        Route::post('/step/2', [ApplicationController::class, 'storeStep2'])->name('step2.store');
        Route::post('/step/3', [ApplicationController::class, 'storeStep3'])->name('step3.store');
        Route::post('/step/4', [ApplicationController::class, 'storeStep4'])->name('step4.store');
        Route::post('/step/5', [ApplicationController::class, 'storeStep5'])->name('step5.store');
        Route::post('/step/6', [ApplicationController::class, 'storeStep6'])->name('step6.store');
        Route::post('/step/7', [ApplicationController::class, 'storeStep7'])->name('step7.store');
        Route::post('/step/8', [ApplicationController::class, 'storeStep8'])->name('step8.store');

        // Fallback GET for steps
        Route::get('/step/{step}', [ApplicationController::class, 'showStep'])->name('step');
    });

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/download', [StudentDashboardController::class, 'downloadProfile'])->name('profile.download');
    Route::post('/photo/update', [StudentDashboardController::class, 'updatePhoto'])->name('photo.update');
    Route::post('/courses/register', [StudentDashboardController::class, 'registerCourses'])->name('courses.register');
    // Registration
    Route::prefix('registration')->name('registration.')->group(function () {
        Route::get('/', [StudentRegistrationController::class, 'index'])->name('index');
        Route::get('/create', [StudentRegistrationController::class, 'create'])->name('create');
        Route::post('/', [StudentRegistrationController::class, 'store'])->name('store');
        Route::get('/history', [StudentRegistrationController::class, 'history'])->name('history');
        Route::get('/{registration}', [StudentRegistrationController::class, 'show'])->name('show');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\FinanceController::class, 'index'])->name('index');
        Route::get('/invoices', [\App\Http\Controllers\Student\FinanceController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Student\FinanceController::class, 'showInvoice'])->name('invoice.show');
        Route::get('/payments', [\App\Http\Controllers\Student\FinanceController::class, 'payments'])->name('payments');
        Route::get('/receipts/{payment}', [\App\Http\Controllers\Student\FinanceController::class, 'receipt'])->name('receipt');
        Route::get('/statement', [\App\Http\Controllers\Student\FinanceController::class, 'statement'])->name('statement');
        Route::get('/payment-info', [\App\Http\Controllers\Student\FinanceController::class, 'paymentInfo'])->name('payment_info');
        Route::get('/installment-bill', [\App\Http\Controllers\Student\FinanceController::class, 'printInstallment'])->name('print_installment');
        Route::get('/clearance-required', [\App\Http\Controllers\Student\FinanceController::class, 'clearanceRequired'])->name('clearance_required');
    });

    // Restricted Academic Content
    Route::middleware(['fee_cleared'])->group(function () {
        Route::get('/results-center', [\App\Http\Controllers\Student\ResultsCenterController::class, 'index'])->name('results.index');
        // Route::get('/results', [StudentDashboardController::class, 'results'])->name('results');
        Route::get('/transcript', [StudentDashboardController::class, 'transcript'])->name('transcript'); // Placeholder
        Route::get('/official-results', [StudentDashboardController::class, 'officialResults'])->name('official_results'); // Placeholder
    });

    // Coursework with specific clearance check
    Route::get('/continuous-assessment', [StudentDashboardController::class, 'continuousAssessment'])
        ->middleware(['coursework_clearance'])
        ->name('continuous_assessment');

    // Welfare (Module 7)
    Route::prefix('nhif')->name('nhif.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\NhifController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Student\NhifController::class, 'store'])->name('store');
    });
    Route::prefix('hostel')->name('hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HostelController::class, 'index'])->name('index');
    });
});

// Academic Staff Routes
Route::middleware(['auth', 'role:academic_staff'])->prefix('academic-staff')->name('academic_staff.')->group(function () {
    Route::get('/dashboard', [AcademicStaffDashboardController::class, 'index'])->name('dashboard');
});

// Principal Routes
Route::middleware(['auth', 'role:principal'])->prefix('principal')->name('principal.')->group(function () {
    Route::get('/dashboard', [PrincipalDashboardController::class, 'index'])->name('dashboard');

    // Student Performance
    Route::prefix('student-performance')->name('student-performance.')->group(function () {
        Route::get('/', [StudentPerformanceController::class, 'index'])->name('index');
        Route::get('/programme', [StudentPerformanceController::class, 'programme'])->name('programme');
        Route::get('/cohort', [StudentPerformanceController::class, 'cohort'])->name('cohort');
        Route::get('/at-risk', [StudentPerformanceController::class, 'atRisk'])->name('at-risk');
        Route::get('/list', [StudentPerformanceController::class, 'list'])->name('list');
        Route::get('/export', [StudentPerformanceController::class, 'export'])->name('export');
        Route::get('/student/{student}', [StudentPerformanceController::class, 'show'])->name('show');
    });
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/assignments/{assignment}', [TeacherDashboardController::class, 'show'])->name('assignments.show');
    
    // Results Management
    Route::resource('results', \App\Http\Controllers\Teacher\ResultController::class)->only(['index', 'show', 'update']);
    Route::post('results/{offering}/submit', [\App\Http\Controllers\Teacher\ResultController::class, 'submit'])->name('results.submit');

    // Marks Entry
    Route::post('/marks/{offering}', [\App\Http\Controllers\Teacher\MarksEntryController::class, 'store'])->name('marks.store');
    Route::post('/marks/{offering}/submit', [\App\Http\Controllers\Teacher\MarksEntryController::class, 'submit'])->name('marks.submit');
    Route::post('/marks/{offering}/import', [\App\Http\Controllers\Teacher\MarksEntryController::class, 'import'])->name('marks.import');
    Route::post('/marks/{offering}/toggle-release', [\App\Http\Controllers\Teacher\MarksEntryController::class, 'toggleRelease'])->name('marks.toggle-release');
});

// Accountant Routes
Route::middleware(['auth', 'role:accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Accountant\DashboardController::class, 'index'])->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admissions
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{application}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{application}/approve', [AdmissionController::class, 'approve'])->name('admissions.approve');
    Route::post('/admissions/{application}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
    Route::post('/admissions/{application}/request-correction', [AdmissionController::class, 'requestCorrection'])->name('admissions.request_correction');

    // Students
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/guardian/create', [AdminStudentController::class, 'createGuardian'])->name('students.guardian.create');
    Route::post('/students/{student}/guardian', [AdminStudentController::class, 'storeGuardian'])->name('students.guardian.store');
    
    // Registration Management
    Route::prefix('registrations')->name('registrations.')->group(function () {
        Route::get('/', [AdminRegistrationController::class, 'index'])->name('index');
        Route::get('/rules', [AdminRegistrationController::class, 'rules'])->name('rules');
        Route::post('/rules', [AdminRegistrationController::class, 'storeRule'])->name('rules.store');
        Route::get('/deadlines', [AdminRegistrationController::class, 'deadlines'])->name('deadlines');
        Route::post('/deadlines', [AdminRegistrationController::class, 'storeDeadline'])->name('deadlines.store');
        Route::get('/{registration}', [AdminRegistrationController::class, 'show'])->name('show');
        Route::post('/{registration}/approve', [AdminRegistrationController::class, 'approve'])->name('approve');
        Route::post('/{registration}/reject', [AdminRegistrationController::class, 'reject'])->name('reject');
    });

    // Integration Hub
    Route::get('/integration/logs', [IntegrationController::class, 'logs'])->name('integration.logs');
    Route::post('/integration/verify-necta', [IntegrationController::class, 'verifyNecta'])->name('integration.verify-necta');
    Route::post('/integration/verify-nacte', [IntegrationController::class, 'verifyNacte'])->name('integration.verify-nacte');

    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/create', [ResultController::class, 'create'])->name('results.create');
    Route::post('/results', [ResultController::class, 'store'])->name('results.store');
    
    // Official Results Upload
    Route::get('/results/upload', [ResultController::class, 'uploadOfficial'])->name('results.upload');
    Route::post('/results/upload', [ResultController::class, 'storeOfficial'])->name('results.upload.store');
    Route::post('/results/upload/{upload}/approve', [ResultController::class, 'approveOfficial'])->name('results.upload.approve');
    
    // Module Result Details
    Route::get('/results/{offering}', [ResultController::class, 'show'])->name('results.show');
    Route::post('/results/{offering}/publish', [ResultController::class, 'publish'])->name('results.publish');

    // Downloadables
    Route::get('/downloads', [DownloadableController::class, 'index'])->name('downloads.index');
    Route::post('/downloads', [DownloadableController::class, 'store'])->name('downloads.store');
    Route::delete('/downloads/{downloadable}', [DownloadableController::class, 'destroy'])->name('downloads.destroy');

    // Department & Staff Management
    Route::resource('departments', DepartmentController::class);
    Route::resource('teachers', TeacherController::class);
    
    // Module Assignments
    Route::resource('assignments', ModuleAssignmentController::class);
});

// Finance Management (Admin, Accountant, Principal)
Route::middleware(['auth'])->prefix('admin/finance')->name('admin.finance.')->group(function () {
    
    // Reports
    Route::middleware(['permission:view_finance_reports'])->group(function() {
        Route::get('reports', [\App\Http\Controllers\Admin\Finance\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/collections', [\App\Http\Controllers\Admin\Finance\ReportController::class, 'collections'])->name('reports.collections');
        Route::get('reports/outstanding', [\App\Http\Controllers\Admin\Finance\ReportController::class, 'outstanding'])->name('reports.outstanding');
        Route::get('clearance', [\App\Http\Controllers\Admin\Finance\FeeClearanceController::class, 'index'])->name('clearance.index');
        Route::post('clearance/override', [\App\Http\Controllers\Admin\Finance\FeeClearanceController::class, 'storeOverride'])->name('clearance.override.store');
        Route::delete('clearance/override/{id}', [\App\Http\Controllers\Admin\Finance\FeeClearanceController::class, 'revokeOverride'])->name('clearance.override.revoke');
    });

    // Fee Items (Admin only)
    Route::resource('fee-items', \App\Http\Controllers\Admin\Finance\FeeItemController::class)->middleware('permission:manage_fee_structures');
    
    // Fee Structures
    Route::middleware('permission:view_fee_structures')->group(function() {
        Route::get('fee-structures', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'index'])->name('fee-structures.index');
        Route::get('fee-structures/{feeStructure}', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'show'])->name('fee-structures.show');
    });
    
    Route::middleware('permission:manage_fee_structures')->group(function() {
        Route::get('fee-structures/create', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'create'])->name('fee-structures.create');
        Route::post('fee-structures', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'store'])->name('fee-structures.store');
        Route::get('fee-structures/{feeStructure}/edit', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'edit'])->name('fee-structures.edit');
        Route::put('fee-structures/{feeStructure}', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'update'])->name('fee-structures.update');
        Route::delete('fee-structures/{feeStructure}', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'destroy'])->name('fee-structures.destroy');
        
        Route::post('fee-structures/{feeStructure}/publish', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'publish'])->name('fee-structures.publish');
        Route::post('fee-structures/{feeStructure}/archive', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'archive'])->name('fee-structures.archive');
        Route::post('fee-structures/{feeStructure}/duplicate', [\App\Http\Controllers\Admin\Finance\FeeStructureController::class, 'duplicate'])->name('fee-structures.duplicate');
    });

    // Invoices
    Route::middleware('permission:view_invoices')->group(function() {
        Route::get('invoices', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'show'])->name('invoices.show');
        // AJAX
        Route::get('ajax/students/search', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'searchStudents'])->name('invoices.search-students');
        Route::get('ajax/students/{student}/context', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'getStudentContext'])->name('invoices.student-context');
        Route::get('ajax/fee-structure', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'getFeeStructure'])->name('invoices.get-fee-structure');
    });

    Route::middleware('permission:create_invoice')->group(function() {
        Route::get('invoices/create', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('invoices/{invoice}/void', [\App\Http\Controllers\Admin\Finance\InvoiceController::class, 'void'])->name('invoices.void');
    });

    // Payments
    Route::middleware('permission:view_finance_reports')->group(function() { // Using view_finance_reports as proxy for viewing payments list for now, or add specific permission
         Route::get('payments', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'index'])->name('payments.index');
         Route::get('payments/{payment}', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'show'])->name('payments.show');
         Route::get('payments/student/{student}/invoices', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'getStudentInvoices'])->name('payments.student-invoices');
    });

    Route::middleware('permission:record_payments')->group(function() {
        Route::get('payments/create', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'store'])->name('payments.store');
    });
    
    Route::middleware('permission:reverse_payments')->group(function() {
        Route::post('payments/{payment}/reverse', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'reverse'])->name('payments.reverse');
    });

    Route::middleware('permission:download_receipts')->group(function() {
        Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Admin\Finance\PaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    });
});

// Welfare Management (Admin, Welfare Officer, Principal)
Route::middleware(['auth'])->prefix('admin/welfare')->name('admin.welfare.')->group(function () {
    
    // NHIF
    Route::middleware('permission:view_nhif')->group(function() {
        Route::get('nhif', [\App\Http\Controllers\Admin\Welfare\NhifController::class, 'index'])->name('nhif.index');
    });
    Route::middleware('permission:manage_nhif')->group(function() {
        Route::post('nhif/{membership}/verify', [\App\Http\Controllers\Admin\Welfare\NhifController::class, 'verify'])->name('nhif.verify');
        Route::put('nhif/{membership}', [\App\Http\Controllers\Admin\Welfare\NhifController::class, 'update'])->name('nhif.update');
    });

    // Hostels
    Route::middleware('permission:view_hostels')->group(function() {
        Route::get('hostels', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'index'])->name('hostels.index');
        Route::get('hostels/allocations', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'allocations'])->name('hostels.allocations');
        Route::get('hostels/{hostel}', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'show'])->name('hostels.show');
        Route::get('hostels/ajax/available-beds', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'getAvailableBeds'])->name('hostels.available-beds');
    });
    
    Route::middleware('permission:allocate_hostels')->group(function() {
        Route::get('hostels/allocations/create', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'createAllocation'])->name('hostels.allocate');
        Route::post('hostels/allocations', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'storeAllocation'])->name('hostels.store-allocation');
        Route::delete('hostels/allocations/{allocation}', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'deallocate'])->name('hostels.deallocate');
    });

    Route::middleware('permission:manage_hostels')->group(function() {
        Route::post('hostels/allocations/{allocation}/checkout', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'checkout'])->name('hostels.checkout');
        Route::post('hostels/allocations/{allocation}/cancel', [\App\Http\Controllers\Admin\Welfare\HostelController::class, 'cancel'])->name('hostels.cancel');
    });

    // Reports
    Route::middleware('permission:view_welfare_reports')->group(function() {
        Route::get('reports/nhif', [\App\Http\Controllers\Admin\Welfare\ReportController::class, 'nhif'])->name('reports.nhif');
        Route::get('reports/hostel', [\App\Http\Controllers\Admin\Welfare\ReportController::class, 'hostel'])->name('reports.hostel');
    });
});

// Module 8: Evaluation System Routes
Route::middleware(['auth', 'role:student'])->prefix('student/evaluation')->name('student.evaluation.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Student\EvaluationController::class, 'index'])->name('index');
    Route::get('/{evaluation}', [\App\Http\Controllers\Student\EvaluationController::class, 'show'])->name('show');
    Route::post('/{evaluation}', [\App\Http\Controllers\Student\EvaluationController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:admin|academic_staff|principal'])->prefix('admin/evaluation')->name('admin.evaluation.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\EvaluationController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [\App\Http\Controllers\Admin\EvaluationController::class, 'reports'])->name('reports');
    
    // Templates & Questions (Admin/Academic)
    Route::middleware('permission:manage_evaluation_templates')->group(function() {
        Route::resource('templates', \App\Http\Controllers\Admin\EvaluationTemplateController::class);
    });

    // Periods (Admin/Academic)
    Route::middleware('permission:open_close_evaluations')->group(function() {
        Route::resource('periods', \App\Http\Controllers\Admin\EvaluationPeriodController::class);
        Route::post('generate', [\App\Http\Controllers\Admin\EvaluationPeriodController::class, 'generateEvaluations'])->name('generate');
    });
});

// Module 8: SMS System Routes
Route::middleware(['auth'])->prefix('admin/communication/sms')->name('admin.communication.sms.')->group(function () {
    
    // Settings (Admin only)
    Route::middleware('permission:manage_sms_settings')->group(function() {
        Route::get('settings', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'settings'])->name('settings');
        Route::post('settings', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'updateSettings'])->name('settings.update');
    });

    // Send Bulk (Admin/Accountant/Academic)
    Route::middleware('permission:send_bulk_sms')->group(function() {
        Route::get('send', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'index'])->name('index'); // Send page
        Route::post('send', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'send'])->name('send');
        Route::post('preview', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'preview'])->name('preview');
        
        // Templates
        Route::resource('templates', \App\Http\Controllers\Admin\Communication\SmsTemplateController::class);
    });

    // Logs (View permission)
    Route::middleware('permission:view_sms_logs')->group(function() {
        Route::get('logs', [\App\Http\Controllers\Admin\Communication\SmsController::class, 'logs'])->name('logs');
    });
});

// Academic Setup (Shared by Admin and Academic Staff)
Route::middleware(['auth', 'role:admin|academic_staff'])->prefix('academic-setup')->name('academic-setup.')->group(function () {
    Route::resource('programs', ProgramController::class);
    Route::resource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{academic_year}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
    Route::resource('semesters', SemesterController::class)->only(['index']);
    Route::resource('modules', ModuleController::class);
    Route::resource('module-offerings', ModuleOfferingController::class);
});

// Principal Module Routes
Route::middleware(['auth', 'role:principal'])->prefix('principal')->name('principal.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PrincipalDashboardController::class, 'index'])->name('dashboard');

    // Academic Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AcademicReportsController::class, 'index'])->name('index');
        Route::get('/performance', [AcademicReportsController::class, 'performance'])->name('performance');
        Route::get('/registration', [AcademicReportsController::class, 'registration'])->name('registration');
        Route::get('/progression', [AcademicReportsController::class, 'progression'])->name('progression');
        Route::get('/workflow', [AcademicReportsController::class, 'workflow'])->name('workflow');
        Route::get('/compliance', [AcademicReportsController::class, 'compliance'])->name('compliance');
        Route::get('/export', [AcademicReportsController::class, 'export'])->name('export');
    });

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Principal\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Principal\ProfileController::class, 'update'])->name('profile.update');

    // Teacher Performance Module
    Route::controller(\App\Http\Controllers\Principal\TeacherPerformanceController::class)->prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/alerts', 'alerts')->name('alerts');
        Route::get('/reports', 'reports')->name('reports');
        Route::get('/export', 'export')->name('export');
        Route::get('/module/{moduleOffering}', 'module')->name('module');
        Route::get('/{teacher}', 'show')->name('show');
    });

    // Other Governance Links (Placeholders)
    // Route::get('/teachers', function() { return view('principal.placeholder', ['title' => 'Teacher Performance']); })->name('teachers');
    Route::get('/academic-reports', function() { return redirect()->route('principal.reports.index'); }); // Redirect old link



    // Communication Module
    Route::controller(\App\Http\Controllers\Principal\CommunicationController::class)->prefix('communication')->name('communication.')->group(function () {
        Route::get('/', 'index')->name('index'); // Inbox
        Route::get('/sent', 'sent')->name('sent');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/message/{message}', 'show')->name('show');
        Route::post('/message/{message}/reply', 'reply')->name('reply');
        Route::get('/report/{id}/{type}', 'deliveryReport')->name('report');
        Route::get('/export/{id}/{type}/{format}', 'exportReport')->name('export');

        // Announcements
        Route::get('/announcements', 'announcements')->name('announcements');
        Route::get('/announcements/{id}/edit', 'editAnnouncement')->name('announcements.edit');
        Route::put('/announcements/{id}', 'updateAnnouncement')->name('announcements.update');
    });
});

require __DIR__.'/auth.php';
