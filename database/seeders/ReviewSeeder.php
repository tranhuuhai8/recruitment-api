<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Company;
use App\Models\Review;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    use GeneratesTimestamps;
    use ResolvesDemoAccounts;

    private const TARGET = 50;

    private const WINDOW_START = '2026-05-07';

    private const DEMO_COMPANY_MIN_REVIEWS = 8;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicantIds = Applicant::pluck('id')->all();
        $companyIds = Company::pluck('id')->all();

        $remaining = self::TARGET - Review::count();
        if ($remaining > 0) {
            $this->createReviews($remaining, $applicantIds, $companyIds);
        }

        $demoCompanyId = $this->demoCompanyId();
        if ($demoCompanyId) {
            $shortfall = self::DEMO_COMPANY_MIN_REVIEWS - Review::where('company_id', $demoCompanyId)->count();
            if ($shortfall > 0) {
                $this->createReviews($shortfall, $applicantIds, [$demoCompanyId]);
            }
        }
    }

    /**
     * @param array<int, int> $applicantIds
     * @param array<int, int> $companyIds
     */
    private function createReviews(int $count, array $applicantIds, array $companyIds): void
    {
        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 60, 2880);

        foreach ($timestamps as $timestamp) {
            Review::factory()->create([
                'applicant_id' => fake()->randomElement($applicantIds),
                'company_id' => fake()->randomElement($companyIds),
                'created_at' => $timestamp['created_at'],
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
