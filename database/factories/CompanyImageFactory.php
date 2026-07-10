<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyImage>
 */
class CompanyImageFactory extends Factory
{
    protected $model = CompanyImage::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'url' => fake()->unique()->imageUrl(800, 600, 'business'),
            'order_number' => fake()->numberBetween(1, 5),
        ];
    }
}
