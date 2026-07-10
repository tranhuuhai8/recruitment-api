<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\ApplicationFile;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationFile>
 */
class ApplicationFileFactory extends Factory
{
    protected $model = ApplicationFile::class;

    public function definition(): array
    {
        $fileName = 'CV_' . Str::slug(VietnameseFaker::personName()) . '-' . fake()->unique()->numerify('####') . '.pdf';

        return [
            'applicant_id' => Applicant::factory(),
            'file_name' => $fileName,
            'file_path' => 'pdfs/' . $fileName,
            'file_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(50_000, 3_000_000),
            'order' => fake()->numberBetween(1, 5),
        ];
    }
}
