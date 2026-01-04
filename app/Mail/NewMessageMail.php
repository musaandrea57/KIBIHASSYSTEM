<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageModel;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message)
    {
        $this->messageModel = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Official Communication: ' . $this->messageModel->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_message',
            with: [
                'subject' => $this->messageModel->subject,
                'senderName' => $this->messageModel->sender->name ?? 'KIBIHAS Administration',
                'body' => $this->messageModel->body,
                'priority' => $this->messageModel->priority ?? 'normal',
                'classification' => $this->messageModel->classification ?? 'General',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
