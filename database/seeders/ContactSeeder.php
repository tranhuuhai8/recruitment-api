<?php

namespace Database\Seeders;

use App\Models\Contact;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    use GeneratesTimestamps;

    private const TARGET = 100;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $remaining = self::TARGET - Contact::count();
        if ($remaining <= 0) {
            return;
        }

        $timestamps = $this->sequentialTimestamps($remaining, '2026-05-10', 20, 1440);

        foreach ($timestamps as $timestamp) {
            $createdAt = $timestamp['created_at'];
            $status = fake()->randomElement(Contact::CONTACT_STATUSES);

            Contact::factory()->create([
                'status' => $status,
                'replied_at' => $status === Contact::STATUS_RESOLVED
                    ? $this->afterWithFloor($createdAt, 30, 4320, null)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
