<?php

namespace App\Mail;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cart;
    public $subject;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Cart $cart, string $subject = null, string $message = null)
    {
        $this->user = $user;
        $this->cart = $cart;
        $this->subject = $subject ?: 'تذكير: لديك منتجات في سلة التسوق';
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.abandoned-cart',
            with: [
                'user' => $this->user,
                'cart' => $this->cart,
                'message' => $this->message,
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

