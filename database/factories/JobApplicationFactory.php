<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobApplication;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        $fileName = 'cv-' . fake()->unique()->numerify('########') . '.pdf';

        return [
            'applicant_id' => Applicant::factory(),
            'job_id' => Job::factory(),
            'file_name' => $fileName,
            'file_path' => 'pdfs/' . $fileName,
            'application_file_id' => null,
            'cover_letter' => VietnameseFaker::coverLetter(),
            'guest_name' => null,
            'guest_email' => null,
            'guest_telephone' => null,
            'status' => fake()->randomElement(JobApplication::STATUSES),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'applicant_id' => null,
            'guest_name' => VietnameseFaker::personName(),
            'guest_email' => fake()->safeEmail(),
            'guest_telephone' => fake()->numerify('0#########'),
        ]);
    }
}
