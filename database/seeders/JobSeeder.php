<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Job;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Database\Seeders\Concerns\SkewsDistribution;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    use GeneratesTimestamps;
    use SkewsDistribution;
    use ResolvesDemoAccounts;

    // Kept well above 300 so the volume requirement holds even after demo top-up.
    private const TARGET = 320;

    private const WINDOW_START = '2026-05-03';

    // The demo company (user id linked to company@gmail.com) should always
    // stand out with plenty of jobs to make manual testing easier.
    private const DEMO_COMPANY_MIN_JOBS = 25;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoCompanyId = $this->demoCompanyId();

        $remaining = self::TARGET - Job::count();
        if ($remaining > 0) {
            $this->createJobs($remaining, $demoCompanyId);
        }

        if ($demoCompanyId) {
            $shortfall = self::DEMO_COMPANY_MIN_JOBS - Job::where('company_id', $demoCompanyId)->count();
            if ($shortfall > 0) {
                $this->createJobs($shortfall, $demoCompanyId, forceCompanyId: $demoCompanyId);
            }
        }
    }

    private function createJobs(int $count, ?int $demoCompanyId, ?int $forceCompanyId = null): void
    {
        $companyIds = Company::pluck('id')->all();
        $pool = $forceCompanyId
            ? [$forceCompanyId]
            : $this->weightedPool($companyIds, $demoCompanyId, hotCount: 12);

        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 10, 350);

        foreach ($timestamps as $timestamp) {
            $createdAt = $timestamp['created_at'];
            $notifyFrequency = fake()->randomElement(Job::JOB_NOTIFY);

            $startDate = $createdAt->copy()->addDays(fake()->numberBetween(0, 10));
            $endDate = $startDate->copy()->addDays(fake()->numberBetween(15, 90));

            $lastSentNotify = null;
            if ($notifyFrequency > 0 && fake()->boolean(50)) {
                $lastSentNotify = $this->afterWithFloor($createdAt, 60, 20000, null);
            }

            Job::factory()->create([
                'company_id' => fake()->randomElement($pool),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'notify_frequency' => $notifyFrequency,
                'last_sent_notify' => $lastSentNotify,
                'created_at' => $createdAt,
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
