<?php

namespace App\Jobs;

use App\Mail\MailNewJobToFollower;
use App\Models\CompanyFollower;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNewJobToFollowers implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $companyId,
        protected array $companyData,
        protected array $jobData,
        protected string $jobUrl,
    ) {
    }

    public function handle(): void
    {
        CompanyFollower::query()
            ->where('company_id', $this->companyId)
            ->where('notify_new_job', true)
            ->whereHas('applicant.user', function ($q) {
                $q->where('status', User::STATUS_ACTIVE);
            })
            ->with(['applicant.user:id,mail_address,status'])
            ->chunkById(200, function ($followers) {
                $emails = $followers->pluck('applicant.user.mail_address')->filter();

                foreach ($emails as $email) {
                    Mail::to($email)->queue(new MailNewJobToFollower(
                        $this->jobData,
                        $this->companyData,
                        $this->jobUrl,
                    ));
                }
            });
    }
}
