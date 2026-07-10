<?php

namespace Database\Factories;

use App\Models\MailTemplate;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailTemplate>
 */
class MailTemplateFactory extends Factory
{
    protected $model = MailTemplate::class;

    public function definition(): array
    {
        $name = VietnameseFaker::mailTemplateName();

        return [
            'name' => $name,
            'code' => fake()->unique()->slug(3),
            'subject' => $name,
            'body' => '<p>Xin chào {{full_name}},</p><p>' . VietnameseFaker::mailBodyParagraph() . '</p>',
            'variables' => fake()->randomElements(
                ['full_name', 'title', 'content', 'company_name'],
                fake()->numberBetween(1, 3)
            ),
            'type' => fake()->randomElement(MailTemplate::TEMPLATE_TYPES),
            'is_active' => fake()->boolean(90),
        ];
    }
}
