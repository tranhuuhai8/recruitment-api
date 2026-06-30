<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailSavedJobDeadlineReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected array $jobData,
        protected string $jobUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Nhắc nhở: Công việc bạn lưu sắp hết hạn',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.saved_job_deadline_reminder',
            with: [
                'jobData' => $this->jobData,
                'jobUrl' => $this->jobUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
