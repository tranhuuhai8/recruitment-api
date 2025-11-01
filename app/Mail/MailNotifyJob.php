<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNotifyJob extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $jobData;
    protected $applications;

    /**
     * Create a new message instance.
     */
    public function __construct($jobData, $applications)
    {
        $this->jobData = $jobData;
        $this->applications = $applications;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Mail thông báo tình trạng ứng tuyển',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.notify_job',
            with: [
                'jobData' => $this->jobData,
                'jobApplications' => $this->applications,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
