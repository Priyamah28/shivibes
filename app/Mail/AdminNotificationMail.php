<?php

namespace App\Mail;

use App\Mail\Concerns\UsesShivibesMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    /**
     * @param  array<string, mixed>  $details
     */
    public function __construct(
        public string $title,
        public string $message,
        public array $details = [],
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.html.admin-notification',
            with: [
                'title' => $this->title,
                'bodyMessage' => $this->message,
                'details' => $this->details,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel,
            ],
        );
    }
}
