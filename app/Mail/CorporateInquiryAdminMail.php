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

class CorporateInquiryAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(public CorporateInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        $company = $this->inquiry->company_name ?: $this->inquiry->name;

        return new Envelope(
            from: $this->shivibesFrom(),
            replyTo: [$this->inquiry->email],
            subject: 'New corporate inquiry — '.$company,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.html.corporate-inquiry-admin',
            with: [
                'inquiry' => $this->inquiry,
                'adminUrl' => route('admin.inquiries.index'),
            ],
        );
    }
}
