<?php

namespace App\Jobs;

use App\Mail\MailNotifyJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMailNotifyJob implements ShouldQueue
{
    use Queueable;

    protected $jobData;
    protected $email;
    protected $applications;

    /**
     * Create a new job instance.
     */
    public function __construct($jobData, $email, $applications)
    {
        $this->jobData = $jobData;
        $this->email = $email;
        $this->applications = $applications;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->email) {
            Mail::to($this->email)->queue(new MailNotifyJob($this->jobData, $this->applications));
        }
    }
}
