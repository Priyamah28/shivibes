<?php

namespace App\Mail;

use App\Mail\Concerns\UsesShivibesMailer;
use App\Models\CorporateInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CorporateInquiryCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(public CorporateInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            replyTo: $this->supportReplyTo(),
            subject: 'We received your corporate inquiry — Shivibes',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.html.corporate-inquiry-customer',
            with: [
                'inquiry' => $this->inquiry,
                'corporateUrl' => route('corporate.create'),
            ],
        );
    }
}
