<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyImage;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Illuminate\Database\Seeder;

class CompanyImageSeeder extends Seeder
{
    use GeneratesTimestamps;
    use ResolvesDemoAccounts;

    private const TARGET = 50;

    private const WINDOW_START = '2026-05-05';

    private const DEMO_COMPANY_MIN_IMAGES = 6;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyIds = Company::pluck('id')->all();

        $remaining = self::TARGET - CompanyImage::count();
        if ($remaining > 0) {
            $this->createImages($remaining, $companyIds);
        }

        $demoCompanyId = $this->demoCompanyId();
        if ($demoCompanyId) {
            $shortfall = self::DEMO_COMPANY_MIN_IMAGES - CompanyImage::where('company_id', $demoCompanyId)->count();
            if ($shortfall > 0) {
                $this->createImages($shortfall, [$demoCompanyId]);
            }
        }
    }

    /**
     * @param array<int, int> $companyIds
     */
    private function createImages(int $count, array $companyIds): void
    {
        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 60, 2880);

        foreach ($timestamps as $timestamp) {
            CompanyImage::factory()->create([
                'company_id' => fake()->randomElement($companyIds),
                'created_at' => $timestamp['created_at'],
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
