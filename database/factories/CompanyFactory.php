<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Company;
use App\Models\User;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->company(),
            'city_id' => fn () => City::inRandomOrder()->value('id'),
            'logo' => fake()->imageUrl(200, 200, 'business'),
            'cover_img' => fake()->imageUrl(1200, 400, 'business'),
            'name' => VietnameseFaker::companyName(),
            'short_name' => strtoupper(fake()->unique()->bothify('CTY##??')),
            'telephone' => fake()->unique()->numerify('0#########'),
            'website' => fake()->url(),
            'address' => VietnameseFaker::address(),
            'description' => VietnameseFaker::companyDescription(),
        ];
    }
}
