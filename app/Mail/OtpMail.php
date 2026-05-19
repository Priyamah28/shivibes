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

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(
        public User $user,
        public string $otp,
        public int $expiresMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            subject: 'Your Shivibes verification code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.html.otp',
            with: [
                'userName' => $this->user->name,
                'otp' => $this->otp,
                'expiresMinutes' => $this->expiresMinutes,
            ],
        );
    }
}
