<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $body,
        public string $actionUrl,
        public string $actionLabel,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-notification',
            with: [
                'title' => $this->title,
                'body' => $this->body,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel,
                'appName' => config('app.name', 'Aperlex'),
            ],
        );
    }
}
