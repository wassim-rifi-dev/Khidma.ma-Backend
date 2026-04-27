<?php

namespace App\Mail\Common;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $heading;
    public string $greeting;
    public string $messageLine;
    public array $details;
    public ?string $actionLabel;
    public ?string $actionUrl;
    public ?string $footerLine;

    public function __construct(
        public string $mailSubject,
        string $heading,
        string $greeting,
        string $messageLine,
        array $details = [],
        ?string $actionLabel = null,
        ?string $actionUrl = null,
        ?string $footerLine = null,
    ) {
        $this->heading = $heading;
        $this->greeting = $greeting;
        $this->messageLine = $messageLine;
        $this->details = $details;
        $this->actionLabel = $actionLabel;
        $this->actionUrl = $actionUrl;
        $this->footerLine = $footerLine;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'Mail.Common.ActionNotification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
