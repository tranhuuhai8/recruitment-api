<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', '+1 month');
        $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(15, 90) . ' days');

        return [
            'company_id' => Company::factory(),
            'title' => VietnameseFaker::jobTitle(),
            'banner' => fake()->imageUrl(1200, 400, 'business'),
            'number_of_recruitment' => (string) fake()->numberBetween(1, 20),
            'job_category_id' => fn () => JobCategory::inRandomOrder()->value('id'),
            'city_id' => fn () => City::inRandomOrder()->value('id'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'salary_min' => fake()->numberBetween(8, 15) * 1_000_000,
            'salary_max' => fake()->numberBetween(16, 50) * 1_000_000,
            'description' => VietnameseFaker::jobDescription(),
            'notify_frequency' => fake()->randomElement(Job::JOB_NOTIFY),
            'type' => fake()->randomElement(Job::JOB_TYPES),
            'status' => fake()->randomElement(Job::JOB_STATUSES),
            'last_sent_notify' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
