<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
            CitySeeder::class,
            JobCategorySeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            ApplicantSeeder::class,
            JobSeeder::class,
            CompanyImageSeeder::class,
            ApplicationFileSeeder::class,
            JobApplicationSeeder::class,
            JobFavoriteSeeder::class,
            CompanyFollowerSeeder::class,
            ContactSeeder::class,
            MailTemplateSeeder::class,
            MailLogSeeder::class,
        ]);

    }
}
