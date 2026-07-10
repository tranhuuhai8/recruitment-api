<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Company;
use App\Models\CompanyFollower;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Illuminate\Database\Seeder;

class CompanyFollowerSeeder extends Seeder
{
    use GeneratesTimestamps;
    use ResolvesDemoAccounts;

    private const TARGET = 50;

    private const WINDOW_START = '2026-05-07';

    private const DEMO_COMPANY_MIN_FOLLOWERS = 15;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seen = [];
        foreach (CompanyFollower::select('applicant_id', 'company_id')->get() as $row) {
            $seen[$row->applicant_id . '-' . $row->company_id] = true;
        }

        $applicantIds = Applicant::pluck('id')->all();
        $companyIds = Company::pluck('id')->all();

        $remaining = self::TARGET - CompanyFollower::count();
        if ($remaining > 0) {
            $this->createFollowers($remaining, $applicantIds, $companyIds, $seen);
        }

        $demoCompanyId = $this->demoCompanyId();
        if ($demoCompanyId) {
            $shortfall = self::DEMO_COMPANY_MIN_FOLLOWERS
                - CompanyFollower::where('company_id', $demoCompanyId)->count();
            if ($shortfall > 0) {
                $this->createFollowers($shortfall, $applicantIds, [$demoCompanyId], $seen);
            }
        }

        $demoApplicantId = $this->demoApplicantId();
        if ($demoApplicantId && $demoCompanyId && !isset($seen[$demoApplicantId . '-' . $demoCompanyId])) {
            $this->createFollowers(1, [$demoApplicantId], [$demoCompanyId], $seen);
        }
    }

    /**
     * @param array<int, int> $applicantIds
     * @param array<int, int> $companyIds
     * @param array<string, bool> $seen
     */
    private function createFollowers(int $count, array $applicantIds, array $companyIds, array &$seen): void
    {
        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 30, 1440);

        $created = 0;
        $attempts = 0;
        $maxAttempts = $count * 20;

        while ($created < $count && $attempts < $maxAttempts) {
            $attempts++;
            $applicantId = $applicantIds[array_rand($applicantIds)];
            $companyId = $companyIds[array_rand($companyIds)];
            $key = $applicantId . '-' . $companyId;

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            CompanyFollower::factory()->create([
                'applicant_id' => $applicantId,
                'company_id' => $companyId,
                'created_at' => $timestamps[$created]['created_at'],
                'updated_at' => $timestamps[$created]['updated_at'],
            ]);
            $created++;
        }
    }
}
