<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\Jobs\SendMailNotifyJob;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Log;

class SendNotifyFrequencyJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:frequency-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email tổng hợp ứng viên cho từng job theo tần suất được cấu hình';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Job::with(['company.user'])
                ->where('notify_frequency', '>', 0)
                ->where('status', Job::STATUS_OPEN)
                ->whereRelation('company.user', 'deleted_at', null)
                ->whereRelation('company.user', 'status', User::STATUS_ACTIVE)
                ->chunk(100, function ($jobs) {
                    $jobIds = [];
                    foreach ($jobs as $job) {
                        $from = Carbon::parse($job->last_sent_notify ?? $job->created_at)->startOfDay();
                        $to = now()->startOfDay();

                        if ($from->diffInDays($to) < $job->notify_frequency) {
                            continue;
                        }

                        $applications = $job->applications()
                            ->with(['applicant.user', 'applicationFile'])
                            ->whereBetween('created_at', [$from, $to])
                            ->get();

                        if ($applications) {
                            $jobIds[] = $job->id;

                            SendMailNotifyJob::dispatch(
                                $this->makeDataJob($job),
                                $job->company?->user?->mail_address,
                                CommonHelper::makeDataJobApplication($applications)
                            );
                        }
                    }

                    Job::query()
                        ->whereIn('id', $jobIds)
                        ->update(['last_sent_notify' => now()]);
                });
            Log::info('SendNotifyFrequencyJob executed successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * makeDataJob
     *
     * @param  $job
     * @return array
     */
    protected function makeDataJob($job): array
    {
        return [
            'title' => $job->title,
            'last_sent_notify' => $job->last_sent_notify->format('d/m/Y'),
            'created_at' => $job->created_at->format('d/m/Y'),
            'company_name' => $job->company?->name ?? '',
        ];
    }
}
