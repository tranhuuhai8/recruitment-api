<?php

namespace App\Http\Resources\Admin\MailLog;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'contact_id'       => $this->contact_id,
            'mail_template_id' => $this->mail_template_id,
            'from_email'       => $this->from_email,
            'to_email'         => $this->to_email,
            'to_name'          => $this->to_name,
            'subject'          => $this->subject,
            'body'             => $this->body,
            'status'           => $this->status,
            'sent_at'          => DateHelper::parseDateBe($this->sent_at),
            'failed_reason'    => $this->failed_reason,
            'mail_template'    => $this->whenLoaded('mailTemplate', fn () => [
                'id'   => $this->mailTemplate->id,
                'name' => $this->mailTemplate->name,
                'code' => $this->mailTemplate->code,
            ]),
            'created_at'       => DateHelper::parseOnlyDate($this->created_at),
        ];
    }
}
