<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DigitalCardsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderItems;
    public $customerName;
    public $logoPath;
    public $siteName;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $orderItems, $customerName)
    {
        $this->order = $order;
        $this->orderItems = $orderItems;
        $this->customerName = $customerName;
        $this->logoPath = public_path('assets/defult-logo.jpg');
        $this->siteName = \App\Models\Setting::get('site_name', config('app.name', 'متجر البطاقات الرقمية'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'بطاقاتك الرقمية - طلب رقم ' . $this->order->order_number,
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.digital-cards')
            ->with([
                'order' => $this->order,
                'orderItems' => $this->orderItems,
                'customerName' => $this->customerName,
                'logoPath' => $this->logoPath,
                'siteName' => $this->siteName,
            ]);
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

