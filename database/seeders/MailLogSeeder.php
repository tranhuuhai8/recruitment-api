<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\MailLog;
use App\Models\MailTemplate;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Illuminate\Database\Seeder;

class MailLogSeeder extends Seeder
{
    use GeneratesTimestamps;

    private const TARGET = 50;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $remaining = self::TARGET - MailLog::count();
        if ($remaining <= 0) {
            return;
        }

        $contactIds = Contact::pluck('id')->all();
        $templateIds = MailTemplate::pluck('id')->all();
        $timestamps = $this->sequentialTimestamps($remaining, '2026-05-12', 20, 1440);

        foreach ($timestamps as $timestamp) {
            $createdAt = $timestamp['created_at'];
            $status = fake()->randomElement(MailLog::LOG_STATUSES);

            MailLog::factory()->create([
                'contact_id' => fake()->randomElement($contactIds),
                'mail_template_id' => fake()->randomElement($templateIds),
                'status' => $status,
                'sent_at' => in_array($status, [MailLog::STATUS_SENT, MailLog::STATUS_BOUNCED], true)
                    ? $this->afterWithFloor($createdAt, 1, 120, null)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
