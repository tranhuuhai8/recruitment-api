<?php

namespace App\Jobs;

use App\Mail\ContactReplyMail;
use App\Models\Contact;
use App\Models\MailLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContactReplyMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly MailLog $mailLog
    ) {
    }

    public function handle(): void
    {
        try {
            Mail::to($this->mailLog->to_email, $this->mailLog->to_name)
                ->send(new ContactReplyMail($this->mailLog));

            $this->mailLog->update([
                'status'  => MailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
            if ($this->mailLog->contact_id) {
                Contact::where('id', $this->mailLog->contact_id)->update([
                    'status'     => Contact::STATUS_RESOLVED,
                    'replied_at' => now(),
                ]);
            }
        } catch (Exception $e) {
            Log::error('SendContactReplyMail failed', [
                'mail_log_id' => $this->mailLog->id,
                'to_email'    => $this->mailLog->to_email,
                'error'       => $e->getMessage(),
            ]);

            $this->mailLog->update([
                'status'        => MailLog::STATUS_FAILED,
                'failed_reason' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
