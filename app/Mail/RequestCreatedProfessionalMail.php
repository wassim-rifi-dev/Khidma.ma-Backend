<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestCreatedProfessionalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requestOrder;

    public function __construct($requestOrder)
    {
        $this->requestOrder = $requestOrder;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle demande de service',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.Professional.RequestCreated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
