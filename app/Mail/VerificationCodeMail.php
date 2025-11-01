<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $code, string $type = 'login')
    {
        $this->user = $user;
        $this->code = $code;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'registration' 
            ? 'كود التحقق - إنشاء حساب جديد' 
            : 'كود التحقق - تسجيل الدخول';

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', 'متجر البطاقات الرقمية');

        return new Envelope(
            subject: $subject,
            from: $fromAddress ? new Address($fromAddress, $fromName) : null,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code',
            with: [
                'user' => $this->user,
                'code' => $this->code,
                'type' => $this->type,
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
        return [];
    }
}
