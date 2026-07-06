<?php

namespace App\Console\Commands;

use App\Mail\MailSavedJobDeadlineReminder;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobFavorite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSavedJobDeadlineReminders extends Command
{
    protected $signature = 'notify:saved-job-deadline';
    protected $description = 'Gửi email nhắc hạn nộp cho công việc đã lưu (chưa apply)';

    public function handle(): int
    {
        $daysBefore = (int) env('JOB_DEADLINE_REMINDER_DAYS', 2);
        $today = now()->startOfDay();
        $to = now()->addDays($daysBefore)->endOfDay();

        JobFavorite::query()
            ->whereNull('deleted_at')
            ->where(function ($q) use ($today) {
                $q->whereNull('deadline_reminded_at')
                    ->orWhere('deadline_reminded_at', '<', $today);
            })
            ->with(['applicant.user'])
            ->chunk(200, function ($favorites) use ($today, $to) {
                foreach ($favorites as $favorite) {
                    $applicant = $favorite->applicant;
                    $user = $applicant?->user;

                    if (!$user || $user->status !== User::STATUS_ACTIVE || $user->deleted_at) {
                        continue;
                    }

                    $job = Job::query()
                        ->where('id', $favorite->job_id)
                        ->where('status', Job::STATUS_OPEN)
                        ->first();
                    if (!$job) {
                        continue;
                    }

                    $endDate = Carbon::parse($job->end_date);
                    if ($endDate->lt($today) || $endDate->gt($to)) {
                        continue;
                    }

                    $alreadyApplied = JobApplication::query()
                        ->where('job_id', $job->id)
                        ->where('applicant_id', $applicant->id)
                        ->exists();
                    if ($alreadyApplied) {
                        continue;
                    }

                    $frontendBase = config('app.url_home');
                    $jobUrl = rtrim($frontendBase, '/') . '/viec-lam/' . $job->id;

                    Mail::to($user->mail_address)->queue(new MailSavedJobDeadlineReminder(
                        ['id' => $job->id, 'title' => $job->title, 'end_date' => $job->end_date],
                        $jobUrl,
                    ));

                    $favorite->deadline_reminded_at = now();
                    $favorite->save();
                }
            });

        return self::SUCCESS;
    }
}
