<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Database\Seeders\Concerns\GeneratesTimestamps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MailTemplateSeeder extends Seeder
{
    use GeneratesTimestamps;

    private const TARGET = 15;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Give the two system templates the earliest timestamps so the table's
        // created_at still increases with id once the factory-made rows follow.
        $systemCreatedAt = Carbon::parse('2026-05-01 08:00:00');

        MailTemplate::firstOrCreate(
            ['code' => MailTemplate::CODE_CONTACT_CONFIRMATION],
            [
                'name' => 'Contact Confirmation',
                'subject' => 'Chúng tôi đã nhận được liên hệ của bạn',
                'body' => '<p>Xin chào {{full_name}}, chúng tôi đã nhận được nội dung liên hệ "{{title}}" của bạn.</p>',
                'variables' => ['full_name', 'title'],
                'type' => MailTemplate::TYPE_CONFIRMATION,
                'is_active' => true,
                'created_at' => $systemCreatedAt,
                'updated_at' => $systemCreatedAt,
            ]
        );

        MailTemplate::firstOrCreate(
            ['code' => MailTemplate::CODE_CONTACT_REPLY],
            [
                'name' => 'Contact Reply',
                'subject' => 'Phản hồi liên hệ của bạn',
                'body' => '<p>Xin chào {{full_name}}, đây là phản hồi của chúng tôi cho "{{title}}": {{content}}</p>',
                'variables' => ['full_name', 'title', 'content'],
                'type' => MailTemplate::TYPE_REPLY,
                'is_active' => true,
                'created_at' => $systemCreatedAt->copy()->addMinutes(10),
                'updated_at' => $systemCreatedAt->copy()->addMinutes(10),
            ]
        );

        $remaining = self::TARGET - MailTemplate::count();
        if ($remaining <= 0) {
            return;
        }

        $timestamps = $this->sequentialTimestamps($remaining, '2026-05-10', 60, 2880);

        foreach ($timestamps as $timestamp) {
            MailTemplate::factory()->create([
                'created_at' => $timestamp['created_at'],
                'updated_at' => $timestamp['updated_at'],
            ]);
        }
    }
}
