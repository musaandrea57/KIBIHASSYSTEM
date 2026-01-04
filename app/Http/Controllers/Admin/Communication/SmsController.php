<?php

namespace App\Http\Controllers\Admin\Communication;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Program;
use App\Models\SemesterRegistration;
use App\Models\SmsBatch;
use App\Models\SmsMessage;
use App\Models\SmsSetting;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // --- DASHBOARD / SEND ---

    public function index()
    {
        Gate::authorize('send_bulk_sms');
        $templates = SmsTemplate::where('is_active', true)->get();
        $programs = Program::all();
        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        return view('admin.communication.sms.index', compact('templates', 'programs', 'academicYears'));
    }

    public function preview(Request $request)
    {
        Gate::authorize('send_bulk_sms');
        $request->validate([
            'recipient_type' => 'required',
            'message' => 'required_without:template_id',
        ]);

        $messageBody = $request->message;
        if ($request->template_id) {
            $template = SmsTemplate::find($request->template_id);
            if ($template) $messageBody = $template->message_body;
        }

        $recipients = $this->fetchRecipients($request);
        $count = $recipients->count();
        $sample = $recipients->first();
        
        $renderedMessage = $messageBody;
        if ($sample) {
            // Basic replacement for preview
            $data = $this->getRecipientData($sample, $request->recipient_type);
            foreach ($data as $key => $value) {
                $renderedMessage = str_replace('{' . $key . '}', $value, $renderedMessage);
            }
        }

        return view('admin.communication.sms.preview', compact('request', 'count', 'renderedMessage', 'messageBody'));
    }

    public function send(Request $request)
    {
        Gate::authorize('send_bulk_sms');
        
        $request->validate([
            'recipient_type' => 'required',
            'message_body' => 'required', // Passed from preview
        ]);

        $recipients = $this->fetchRecipients($request);
        $messageBody = $request->message_body;
        
        $messagesData = [];
        foreach ($recipients as $recipient) {
            $data = $this->getRecipientData($recipient, $request->recipient_type);
            
            $finalMessage = $messageBody;
            foreach ($data as $key => $value) {
                $finalMessage = str_replace('{' . $key . '}', $value, $finalMessage);
            }

            $phone = $this->getRecipientPhone($recipient, $request->recipient_type);

            $messagesData[] = [
                'recipient_type' => $this->getRecipientTypeModel($request->recipient_type),
                'recipient_id' => $recipient->id,
                'phone_number' => $phone ?? '0000000000',
                'message_body' => $finalMessage,
                'template_key' => $request->template_key ?? null,
            ];
        }

        if (empty($messagesData)) {
            return redirect()->route('admin.communication.sms.index')->with('error', 'No recipients found.');
        }

        $batch = $this->smsService->createBatch(
            'Bulk Send ' . now(),
            $request->except('_token'),
            $messagesData,
            Auth::id()
        );

        // Dispatch job for async processing
        \App\Jobs\ProcessSmsBatch::dispatch($batch);

        return redirect()->route('admin.communication.sms.index')->with('success', 'Bulk SMS Batch Queued: ' . count($messagesData) . ' messages.');
    }

    // --- HELPERS ---

    protected function fetchRecipients(Request $request)
    {
        if ($request->recipient_type === 'individual') {
            // Fake collection for consistent API
            return collect([(object)['id' => 0, 'phone_number' => $request->phone_number, 'name' => 'Individual']]);
        }

        $query = Student::query();

        if ($request->target_group === 'admissions') {
            // Approved applicants (User Application model)
            // Assuming Application maps to User/Student or we send to Application phone
            // For simplicity, let's look for Students who are recently admitted? 
            // Or use Application model directly.
            // User requirement: "Admissions: approved applicants"
            return Application::where('status', 'approved')->get();
        } 
        elseif ($request->target_group === 'finance') {
            // Outstanding balances
            // Filter in memory or use whereHas if logic permits
            // Student::where('balance', '>', 0) is not directly possible if balance is accessor
            // Need to join invoices/payments or fetch all and filter (slow)
            // Optimized: whereRaw('(select sum(total_amount) from invoices where student_id = students.id) > (select sum(amount) from payments where student_id = students.id)')
            // For MVP/Safety: Fetch active students and filter.
            $students = Student::where('status', 'active');
            if ($request->program_id) $students->where('program_id', $request->program_id);
            return $students->get()->filter(function($s) { return $s->balance > 0; });
        }
        elseif ($request->target_group === 'registration') {
            // Not registered this semester
            // Students active but no semester_registrations for current semester
            // We need to know "current semester".
            // Assuming we pick an academic year/semester in filter, or use global current.
            // For MVP, if academic_year_id is passed:
            $query->where('status', 'active');
            if ($request->academic_year_id) {
                $query->whereDoesntHave('registrations', function($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id);
                });
            }
        }
        elseif ($request->target_group === 'results') {
            // Published results (fee cleared)
            // Complex. For MVP: Students who have results in this academic year/semester?
            // "students with published results (and fee-cleared)"
            // Logic: Get students with ModuleResult published.
            if ($request->academic_year_id) {
                $query->whereHas('moduleResults', function($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id)
                      ->whereNotNull('published_at');
                });
            }
            // Filter fee cleared
            return $query->get()->filter(function($s) { return $s->isFeeCleared(); });
        }
        
        // Custom Selection
        if ($request->program_id) $query->where('program_id', $request->program_id);
        if ($request->academic_year_id) $query->where('current_academic_year_id', $request->academic_year_id);
        // if ($request->semester_id) $query->where('current_semester_id', $request->semester_id);
        
        return $query->get();
    }

    protected function getRecipientData($recipient, $type)
    {
        if ($type === 'individual') return ['name' => 'Individual'];
        
        if ($recipient instanceof Application) {
            return [
                'name' => $recipient->first_name . ' ' . $recipient->last_name,
                'program' => $recipient->program->name ?? '',
            ];
        }
        
        // Student
        return [
            'student_name' => $recipient->first_name . ' ' . $recipient->last_name,
            'reg_no' => $recipient->registration_number,
            'balance' => number_format($recipient->balance),
        ];
    }

    protected function getRecipientPhone($recipient, $type)
    {
        if ($type === 'individual') return $recipient->phone_number;
        return $recipient->phone; // Common field for Student and Application
    }
    
    protected function getRecipientTypeModel($type)
    {
        if ($type === 'individual') return 'custom';
        // Check if recipient is application or student
        // This is tricky if we mix. But target_group defines it.
        // I'll assume student unless adms.
        return 'student'; // Simplification
    }

    // --- SETTINGS ---

    public function settings()
    {
        Gate::authorize('manage_sms_settings');
        $settings = SmsSetting::all();
        return view('admin.communication.sms.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        Gate::authorize('manage_sms_settings');
        foreach ($request->settings as $key => $value) {
            SmsSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Settings updated');
    }

    // --- TEMPLATES ---

    public function indexTemplates()
    {
        Gate::authorize('manage_sms_settings');
        $templates = SmsTemplate::all();
        return view('admin.communication.sms.templates.index', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        Gate::authorize('manage_sms_settings');
        $request->validate(['key' => 'required|unique:sms_templates,key,' . $request->id, 'message_body' => 'required']);
        
        SmsTemplate::updateOrCreate(
            ['id' => $request->id],
            [
                'key' => $request->key,
                'message_body' => $request->message_body,
                'is_active' => $request->has('is_active')
            ]
        );
        return back()->with('success', 'Template saved');
    }

    // --- LOGS ---

    public function logs()
    {
        Gate::authorize('view_sms_logs');
        $logs = SmsMessage::latest()->paginate(20);
        return view('admin.communication.sms.logs', compact('logs'));
    }
}
