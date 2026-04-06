<?php

namespace App\Jobs;

use App\Mail\ContactConfirmationMail;
use App\Models\Contact;
use App\Models\MailLog;
use App\Models\MailTemplate;
use App\Services\Admin\ContactService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContactConfirmationMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly Contact $contact
    ) {
    }

    public function handle(): void
    {
        try {
            $template = MailTemplate::where([
                'code' => MailTemplate::CODE_CONTACT_CONFIRMATION,
                'is_active' => true,
            ])->first();

            $contactService = new ContactService();
            $subject = $template
                ? $contactService->renderTemplate($template->subject, $this->contact)
                : 'Chúng tôi đã nhận được tin nhắn của bạn — ' . config('app.name');

            $mailLog = MailLog::create([
                'contact_id'       => $this->contact->id,
                'mail_template_id' => $template?->id,
                'from_email'       => config('mail.from.address'),
                'to_email'         => $this->contact->email,
                'to_name'          => $this->contact->full_name,
                'subject'          => $subject,
                'body'             => $template
                    ? $contactService->renderTemplate($template->body, $this->contact)
                    : '',
                'status'           => MailLog::STATUS_PENDING,
                'metadata'         => ['type' => 'confirmation'],
            ]);

            Mail::to($this->contact->email, $this->contact->full_name)
                ->send(new ContactConfirmationMail($this->contact));

            $mailLog->update([
                'status'  => MailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::error('SendContactConfirmationMail failed', [
                'contact_id' => $this->contact->id,
                'error'      => $e->getMessage(),
            ]);

            MailLog::where([
                'contact_id'       => $this->contact->id,
                'mail_template_id' => $template?->id,
                'status'           => MailLog::STATUS_PENDING,
                'metadata->type'   => 'confirmation',
            ])
                ->latest()
                ->first()
                ?->update([
                    'status'        => MailLog::STATUS_FAILED,
                    'failed_reason' => $e->getMessage(),
                ]);

            throw $e;
        }
    }
}
