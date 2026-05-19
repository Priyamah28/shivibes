<?php

namespace App\Mail;

use App\Mail\Concerns\UsesShivibesMailer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            replyTo: $this->supportReplyTo(),
            subject: 'Welcome to Shivibes — herbal skincare & gifting',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.html.welcome',
            with: [
                'userName' => $this->user->name,
                'shopUrl' => route('products.index'),
            ],
        );
    }
}
