<?php

namespace Database\Factories;

use App\Models\Contact;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $status = fake()->randomElement(Contact::CONTACT_STATUSES);

        return [
            'full_name' => VietnameseFaker::personName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->numerify('0#########'),
            'title' => VietnameseFaker::contactTitle(),
            'content' => VietnameseFaker::contactContent(),
            'status' => $status,
            'priority' => fake()->randomElement(Contact::CONTACT_PRIORITIES),
            'admin_note' => fake()->optional()->passthrough(VietnameseFaker::adminNote()),
            'replied_at' => $status === Contact::STATUS_RESOLVED
                ? fake()->dateTimeBetween('-1 month', 'now')
                : null,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
