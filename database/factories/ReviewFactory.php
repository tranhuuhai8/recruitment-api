<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Company;
use App\Models\Review;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'company_id' => Company::factory(),
            'rating' => fake()->randomFloat(1, 1, 5),
            'content' => VietnameseFaker::reviewContent(),
        ];
    }
}
