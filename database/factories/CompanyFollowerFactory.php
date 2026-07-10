<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Company;
use App\Models\CompanyFollower;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyFollower>
 */
class CompanyFollowerFactory extends Factory
{
    protected $model = CompanyFollower::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'company_id' => Company::factory(),
            'notify_new_job' => fake()->boolean(80),
        ];
    }
}
