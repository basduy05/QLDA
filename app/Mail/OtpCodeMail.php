<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purposeLabel
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your OTP code for :purpose', ['purpose' => $this->purposeLabel]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-code',
            with: [
                'code' => $this->code,
                'purposeLabel' => $this->purposeLabel,
            ],
        );
    }
}
