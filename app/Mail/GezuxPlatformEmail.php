<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GezuxPlatformEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $heading,
        public string $bodyContent,
        public ?string $actionUrl = null,
        public array $contextData = [],
        public ?string $codePresentation = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'GEZUX — ' . $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.platform-notification',
        );
    }
}
