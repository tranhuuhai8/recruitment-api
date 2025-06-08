<?php

namespace App\Jobs;

use App\Mail\MailRegisterAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailAuth implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $type;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->data->mail_address)->queue($this->getMailable());
    }

    /**
     * getMailable
     *
     * @return Mailable
     */
    protected function getMailable(): Mailable
    {
        if ($this->type === 'register-account') {
            return new MailRegisterAccount($this->data);
        }

        return new MailRegisterAccount($this->data);
    }
}
