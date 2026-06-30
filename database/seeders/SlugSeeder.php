<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Job;
use Illuminate\Database\Seeder;

class SlugSeeder extends Seeder
{
    /**
     * Backfill the `slug` column for existing jobs and companies.
     */
    public function run(): void
    {
        Job::withTrashed()->whereNull('slug')->orderBy('id')->chunkById(100, function ($jobs) {
            foreach ($jobs as $job) {
                $job->refreshSlug();
            }
        });

        Company::withTrashed()->whereNull('slug')->orderBy('id')->chunkById(100, function ($companies) {
            foreach ($companies as $company) {
                $company->refreshSlug();
            }
        });
    }
}
