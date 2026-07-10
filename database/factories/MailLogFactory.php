<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MailLog;
use App\Models\MailTemplate;
use Database\Factories\Support\VietnameseFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailLog>
 */
class MailLogFactory extends Factory
{
    protected $model = MailLog::class;

    public function definition(): array
    {
        $status = fake()->randomElement(MailLog::LOG_STATUSES);
        $toName = VietnameseFaker::personName();

        return [
            'contact_id' => Contact::factory(),
            'mail_template_id' => MailTemplate::factory(),
            'from_email' => fake()->companyEmail(),
            'to_email' => fake()->safeEmail(),
            'to_name' => $toName,
            'subject' => VietnameseFaker::mailTemplateName(),
            'body' => '<p>Xin chào ' . $toName . ',</p><p>' . VietnameseFaker::mailBodyParagraph() . '</p>',
            'status' => $status,
            'sent_at' => in_array($status, [MailLog::STATUS_SENT, MailLog::STATUS_BOUNCED], true)
                ? fake()->dateTimeBetween('-1 month', 'now')
                : null,
            'failed_reason' => $status === MailLog::STATUS_FAILED ? fake()->sentence() : null,
            'metadata' => ['ip' => fake()->ipv4()],
        ];
    }
}
