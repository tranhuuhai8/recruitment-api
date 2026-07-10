<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobFavorite;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobFavorite>
 */
class JobFavoriteFactory extends Factory
{
    protected $model = JobFavorite::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'job_id' => Job::factory(),
            'note' => fake()->optional()->passthrough(VietnameseFaker::jobFavoriteNote()),
            'deadline_reminded_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
