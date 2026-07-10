<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobApplication;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Database\Seeders\Concerns\SkewsDistribution;
use Illuminate\Database\Seeder;

class JobApplicationSeeder extends Seeder
{
    use GeneratesTimestamps;
    use SkewsDistribution;
    use ResolvesDemoAccounts;

    // Scaled up so a meaningful share of the 300+ jobs get real applications.
    private const TARGET = 500;

    private const WINDOW_START = '2026-05-07';

    // The demo applicant (applicant@gmail.com) should have plenty of
    // applications to browse when testing the applicant dashboard.
    private const DEMO_APPLICANT_MIN_APPLICATIONS = 25;

    // A handful of applications should link the demo applicant directly to
    // the demo company's jobs, so both demo accounts see each other.
    private const DEMO_CROSS_LINK_MIN = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoApplicantId = $this->demoApplicantId();
        $demoCompanyId = $this->demoCompanyId();

        $remaining = self::TARGET - JobApplication::count();
        if ($remaining > 0) {
            $this->createApplications($remaining, $demoCompanyId);
        }

        if ($demoApplicantId) {
            $shortfall = self::DEMO_APPLICANT_MIN_APPLICATIONS
                - JobApplication::where('applicant_id', $demoApplicantId)->count();
            if ($shortfall > 0) {
                $this->createApplications($shortfall, $demoCompanyId, forceApplicantId: $demoApplicantId);
            }
        }

        if ($demoApplicantId && $demoCompanyId) {
            $demoCompanyJobIds = Job::where('company_id', $demoCompanyId)->pluck('id')->all();

            if (!empty($demoCompanyJobIds)) {
                $existingCrossLinks = JobApplication::where('applicant_id', $demoApplicantId)
                    ->whereIn('job_id', $demoCompanyJobIds)
                    ->count();

                $crossShortfall = self::DEMO_CROSS_LINK_MIN - $existingCrossLinks;
                if ($crossShortfall > 0) {
                    $this->createApplications(
                        $crossShortfall,
                        $demoCompanyId,
                        forceApplicantId: $demoApplicantId,
                        forceJobIds: $demoCompanyJobIds
                    );
                }
            }
        }
    }

    private function createApplications(
        int $count,
        ?int $demoCompanyId,
        ?int $forceApplicantId = null,
        ?array $forceJobIds = null
    ): void {
        $applicantIds = Applicant::pluck('id')->all();
        $jobIds = Job::pluck('id')->all();

        $jobPool = $forceJobIds ?: $this->weightedPool($jobIds, null, hotCount: 20, hotWeightRange: [8, 20], normalWeightRange: [1, 3]);

        if (!$forceJobIds && $demoCompanyId) {
            $demoCompanyJobIds = Job::where('company_id', $demoCompanyId)->pluck('id')->all();
            foreach ($demoCompanyJobIds as $jobId) {
                $jobPool = array_merge($jobPool, array_fill(0, fake()->numberBetween(10, 20), $jobId));
            }
        }

        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 15, 400);

        foreach ($timestamps as $timestamp) {
            $isGuest = !$forceApplicantId && fake()->boolean(20);
            $factory = $isGuest ? JobApplication::factory()->guest() : JobApplication::factory();

            $factory->create([
                'job_id' => fake()->randomElement($jobPool),
                'applicant_id' => $isGuest ? null : ($forceApplicantId ?? fake()->randomElement($applicantIds)),
                'created_at' => $timestamp['created_at'],
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
