<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\Program;
use App\Models\Semester;
use App\Models\User;
use App\Services\AnnouncementService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\CommunicationReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class CommunicationController extends Controller
{
    protected MessageService $messageService;
    protected AnnouncementService $announcementService;

    public function __construct(MessageService $messageService, AnnouncementService $announcementService)
    {
        $this->messageService = $messageService;
        $this->announcementService = $announcementService;
    }

    // Inbox / Dashboard
    public function index(Request $request)
    {
        $messages = $this->messageService->getInbox(Auth::user(), $request->search);
        return view('principal.communication.index', compact('messages'));
    }

    // Sent Messages
    public function sent(Request $request)
    {
        $messages = $this->messageService->getSentMessages(Auth::user(), $request->search);
        return view('principal.communication.sent', compact('messages'));
    }

    // Compose Message Form
    public function create(Request $request)
    {
        $type = $request->query('type', 'message'); // Default to message
        return view('principal.communication.create', [
            'filterOptions' => $this->getFilterOptions(),
            'type' => $type,
            'item' => null
        ]);
    }

    public function editAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        return view('principal.communication.create', [
            'item' => $announcement,
            'filterOptions' => $this->getFilterOptions(),
            'type' => 'announcement'
        ]);
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        // Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'classification' => 'required|string',
            'priority' => 'required|string',
            'channels' => 'required|array|min:1',
            'target_audience' => 'required|array|min:1',
        ]);

        $data = $request->except(['_token', '_method', 'communication_type', 'files']);
        
        // Handle Status (Draft vs Published)
        $isPublished = $request->input('is_published', 1);
        $data['is_published'] = $isPublished;
        $data['status'] = $isPublished ? 'published' : 'draft';
        if ($isPublished && !$announcement->published_at) {
             $data['published_at'] = now();
        }

        // Handle Audience Mapping (same as store)
        // ... Logic to map request fields to audience array ...
        // For simplicity, we can reuse the logic or extract it to a service.
        // But since we are updating, we can just update the fields.
        
        $announcement->update([
            'title' => $request->title,
            'content' => $request->input('content'),
            'category' => $request->classification,
            'priority' => $request->priority,
            'channels' => $request->channels,
            'audience' => $request->target_audience, // This needs to be processed like in store
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_published' => $data['is_published'],
            'status' => $data['status'],
            'published_at' => $data['published_at'] ?? $announcement->published_at,
        ]);

        // Handle Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $announcement->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }
        
        // Audit Log
        if ($isPublished) {
            \App\Services\AuditService::log('announcement_published', $announcement, [], [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'audience' => $announcement->audience
            ]);
        } else {
             \App\Services\AuditService::log('announcement_updated', $announcement, [], [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'status' => 'draft'
            ]);
        }

        return redirect()->route('principal.communication.announcements')
            ->with('success', $isPublished ? 'Announcement updated and published.' : 'Announcement draft updated.');
    }

    // Send Message or Publish Announcement
    public function store(Request $request)
    {
        $request->validate([
            'communication_type' => 'required|in:message,announcement',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'classification' => 'required|string',
            'priority' => 'required|string', // normal, high, urgent
            'channels' => 'nullable|array', // Required for messages, optional for announcements
            'recipients' => 'nullable|array',
            'filters' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg|max:10240',
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $data = $request->all();
        $data['attachments'] = $attachments;

        if ($request->input('communication_type') === 'announcement') {
            // Map fields for AnnouncementService
            $announcementData = [
                'title' => $data['subject'],
                'content' => $data['body'],
                'category' => $data['classification'],
                'priority' => $data['priority'],
                'audience' => $data['filters'] ?? ['all'],
                'attachments' => $attachments,
                'is_published' => true,
                'start_date' => now(),
            ];
            
            $this->announcementService->createAnnouncement(Auth::user(), $announcementData);
            
            return redirect()->route('principal.communication.announcements')->with('success', 'Institutional announcement published successfully.');
        } else {
            // Default to Message
            if (empty($data['channels'])) {
                return back()->withErrors(['channels' => 'Delivery channels are required for direct messages.'])->withInput();
            }
            
            $this->messageService->sendMessage(Auth::user(), $data);
            
            return redirect()->route('principal.communication.sent')->with('success', 'Official message dispatched successfully.');
        }
    }

    // View Message
    public function show(Message $message)
    {
        // Ensure user is sender or recipient
        $isRecipient = $message->recipients()->where('recipient_id', Auth::id())->exists();
        $isSender = $message->sender_id === Auth::id();

        if (!$isRecipient && !$isSender) {
            abort(403);
        }

        if ($isRecipient) {
            $this->messageService->markAsRead(Auth::user(), $message);
        }

        $message = $this->messageService->getMessageDetails($message);
        return view('principal.communication.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Authorization
        $isRecipient = $message->recipients()->where('recipient_id', Auth::id())->exists();
        $isSender = $message->sender_id === Auth::id();

        if (!$isRecipient && !$isSender) {
            abort(403);
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = [
                    'path' => $file->store('attachments', 'public'),
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $this->messageService->reply(Auth::user(), $message, [
            'body' => $request->body,
            'attachments' => $attachments,
        ]);

        return redirect()->route('principal.communication.show', $message->id)->with('success', 'Reply sent successfully.');
    }




    // Announcements Dashboard
    public function announcements(Request $request)
    {
        $query = Announcement::query();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Default view: Show published and drafts, exclude archived unless asked
            $query->whereIn('status', ['published', 'draft', 'scheduled']);
        }

        $announcements = $query->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('principal.communication.announcements', compact('announcements'));
    }

    public function deliveryReport($id, $type = 'message')
    {
        if ($type === 'message') {
            $item = Message::findOrFail($id);
            if ($item->sender_id !== Auth::id()) {
                abort(403);
            }
            $recipients = $item->recipients()->with('recipient')->paginate(20);
        } else {
            $item = Announcement::findOrFail($id);
             if ($item->created_by !== Auth::id()) {
                abort(403);
            }
            $recipients = $item->reads()->with('user')->paginate(20);
        }

        return view('principal.communication.delivery_report', compact('item', 'recipients', 'type'));
    }

    public function exportReport($id, $type = 'message', $format = 'excel')
    {
         if ($type === 'message') {
            $item = Message::findOrFail($id);
             if ($item->sender_id !== Auth::id()) abort(403);
            $recipients = $item->recipients()->with('recipient')->get();
        } else {
            $item = Announcement::findOrFail($id);
            if ($item->created_by !== Auth::id()) abort(403);
            $recipients = $item->reads()->with('user')->get();
        }

        if ($format === 'excel') {
            return Excel::download(new CommunicationReportExport($recipients, $type), "{$type}_{$id}_report.xlsx");
        } else {
            $pdf = Pdf::loadView('principal.communication.report_pdf', compact('item', 'recipients', 'type'));
            return $pdf->download("{$type}_{$id}_report.pdf");
        }
    }

    public function getFilterOptions()
    {
        return [
            'academic_years' => AcademicYear::orderBy('name', 'desc')->get(),
            'programs' => Program::orderBy('name')->get(),
            'semesters' => Semester::orderBy('name')->get(),
            'nta_levels' => [4, 5, 6, 7, 8],
            'roles' => ['Student', 'Teacher', 'Academic Staff', 'Accountant', 'Parent'], // Static for now, or fetch from Spatie
        ];
    }
}
