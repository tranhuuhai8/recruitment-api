<?php

namespace App\Mail;

use App\Models\MailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly MailLog $mailLog
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address($this->mailLog->to_email, $this->mailLog->to_name)],
            subject: $this->mailLog->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact_reply',
            with: [
                'mailLog' => $this->mailLog,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
