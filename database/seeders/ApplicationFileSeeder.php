<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\ApplicationFile;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Database\Seeders\Concerns\ResolvesDemoAccounts;
use Illuminate\Database\Seeder;

class ApplicationFileSeeder extends Seeder
{
    use GeneratesTimestamps;
    use ResolvesDemoAccounts;

    private const TARGET = 50;

    private const WINDOW_START = '2026-05-05';

    private const DEMO_APPLICANT_MIN_FILES = 6;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicantIds = Applicant::pluck('id')->all();

        $remaining = self::TARGET - ApplicationFile::count();
        if ($remaining > 0) {
            $this->createFiles($remaining, $applicantIds);
        }

        $demoApplicantId = $this->demoApplicantId();
        if ($demoApplicantId) {
            $shortfall = self::DEMO_APPLICANT_MIN_FILES
                - ApplicationFile::where('applicant_id', $demoApplicantId)->count();
            if ($shortfall > 0) {
                $this->createFiles($shortfall, [$demoApplicantId]);
            }
        }
    }

    /**
     * @param array<int, int> $applicantIds
     */
    private function createFiles(int $count, array $applicantIds): void
    {
        $timestamps = $this->sequentialTimestamps($count, self::WINDOW_START, 60, 2880);

        foreach ($timestamps as $timestamp) {
            ApplicationFile::factory()->create([
                'applicant_id' => fake()->randomElement($applicantIds),
                'created_at' => $timestamp['created_at'],
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
