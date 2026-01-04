<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $messagingService;

    public function __construct(MessagingService $messagingService)
    {
        $this->messagingService = $messagingService;
    }

    public function index()
    {
        $messages = Auth::user()->receivedMessages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('messages.index', compact('messages'));
    }

    public function sent()
    {
        $messages = Auth::user()->sentMessages()
            ->with('recipients')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('messages.sent', compact('messages'));
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get(['id', 'name', 'email']);
        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        $this->messagingService->sendMessage(
            Auth::user(),
            $request->recipients,
            $request->subject,
            $request->body,
            'private',
            $request->file('attachments') ?? []
        );

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        // Ensure user is recipient or sender
        $isRecipient = $message->recipients()->where('recipient_id', Auth::id())->exists();
        $isSender = $message->sender_id === Auth::id();

        if (!$isRecipient && !$isSender) {
            abort(403);
        }

        if ($isRecipient) {
            $this->messagingService->markAsRead($message, Auth::user());
        }

        return view('messages.show', compact('message', 'isRecipient', 'isSender'));
    }

    public function acknowledge(Message $message)
    {
        $this->messagingService->acknowledge($message, Auth::user());
        return back()->with('success', 'Message acknowledged.');
    }
}
