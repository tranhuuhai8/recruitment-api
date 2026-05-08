<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNewJobToFollower extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected array $jobData,
        protected array $companyData,
        protected string $jobUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Công ty bạn theo dõi vừa đăng tuyển mới',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.new_job_to_follower',
            with: [
                'jobData' => $this->jobData,
                'companyData' => $this->companyData,
                'jobUrl' => $this->jobUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

