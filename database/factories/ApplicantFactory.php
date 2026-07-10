<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\User;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
{
    protected $model = Applicant::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->applicant(),
            'name' => VietnameseFaker::personName(),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
            'gender' => fake()->randomElement([
                Applicant::GENDER_MALE,
                Applicant::GENDER_FEMALE,
                Applicant::GENDER_OTHER,
            ]),
            'birthday' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'telephone' => fake()->unique()->numerify('0#########'),
            'address' => VietnameseFaker::address(),
            'description' => VietnameseFaker::applicantIntro(),
        ];
    }
}
