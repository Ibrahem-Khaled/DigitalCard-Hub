<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $recipientEmail;
    public $recipientName;
    public $senderName;
    public $attachments;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject,
        string $body,
        string $recipientEmail,
        string $recipientName = null,
        string $senderName = null,
        array $attachments = []
    ) {
        $this->subject = $subject;
        $this->body = $body;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName ?: 'عزيزي العميل';
        $this->senderName = $senderName ?: config('mail.from.name', 'متجر البطاقات الرقمية');
        $this->attachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address');

        return new Envelope(
            subject: $this->subject,
            from: $fromAddress ? new Address($fromAddress, $this->senderName) : null,
            replyTo: $fromAddress ? [new Address($fromAddress, $this->senderName)] : [],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test-email',
            with: [
                'subject' => $this->subject,
                'body' => $this->body,
                'recipientName' => $this->recipientName,
                'senderName' => $this->senderName,
                'sentAt' => now()->format('Y-m-d H:i:s'),
                'siteName' => config('app.name', 'متجر البطاقات الرقمية'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->attachments as $attachment) {
            if (isset($attachment['path']) && file_exists($attachment['path'])) {
                $attachments[] = Attachment::fromPath($attachment['path'])
                    ->as($attachment['name'] ?? basename($attachment['path']))
                    ->withMime($attachment['mime'] ?? mime_content_type($attachment['path']));
            }
        }

        return $attachments;
    }
}


