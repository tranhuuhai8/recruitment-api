<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobFavorite;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Illuminate\Database\Seeder;

class JobFavoriteSeeder extends Seeder
{
    use GeneratesTimestamps;
    use ResolvesDemoAccounts;

    private const TARGET = 200;

    private const WINDOW_START = '2026-05-07';

    private const DEMO_APPLICANT_MIN_FAVORITES = 20;

    private const DEMO_CROSS_LINK_MIN = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoApplicantId = $this->demoApplicantId();
        $demoCompanyId = $this->demoCompanyId();

        $seen = [];
        foreach (JobFavorite::select('applicant_id', 'job_id')->get() as $row) {
            $seen[$row->applicant_id . '-' . $row->job_id] = true;
        }

        $applicantIds = Applicant::pluck('id')->all();
        $jobIds = Job::pluck('id')->all();

        $remaining = self::TARGET - JobFavorite::count();
        if ($remaining > 0) {
            $this->createFavorites($remaining, $applicantIds, $jobIds, $seen);
        }

        if ($demoApplicantId) {
            $shortfall = self::DEMO_APPLICANT_MIN_FAVORITES
                - JobFavorite::where('applicant_id', $demoApplicantId)->count();
            if ($shortfall > 0) {
                $this->createFavorites($shortfall, [$demoApplicantId], $jobIds, $seen);
            }
        }

        if ($demoApplicantId && $demoCompanyId) {
            $demoCompanyJobIds = Job::where('company_id', $demoCompanyId)->pluck('id')->all();

            if (!empty($demoCompanyJobIds)) {
                $existing = JobFavorite::where('applicant_id', $demoApplicantId)
                    ->whereIn('job_id', $demoCompanyJobIds)
                    ->count();

                $crossShortfall = self::DEMO_CROSS_LINK_MIN - $existing;
                if ($crossShortfall > 0) {
                    $this->createFavorites($crossShortfall, [$demoApplicantId], $demoCompanyJobIds, $seen);
                }
            }
        }
    }

    /**
     * @param array<int, int> $seen
     */
    private function createFavorites(int $count, array $applicantIds, array $jobIds, array &$seen): void
    {
        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 30, 1440);

        $created = 0;
        $attempts = 0;
        $maxAttempts = $count * 20;

        while ($created < $count && $attempts < $maxAttempts) {
            $attempts++;
            $applicantId = $applicantIds[array_rand($applicantIds)];
            $jobId = $jobIds[array_rand($jobIds)];
            $key = $applicantId . '-' . $jobId;

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $createdAt = $timestamps[$created]['created_at'];

            JobFavorite::factory()->create([
                'applicant_id' => $applicantId,
                'job_id' => $jobId,
                'deadline_reminded_at' => fake()->boolean(30)
                    ? $this->afterWithFloor($createdAt, 60, 20000, null)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $timestamps[$created]['updated_at'],
            ]);
            $created++;
        }
    }
}
