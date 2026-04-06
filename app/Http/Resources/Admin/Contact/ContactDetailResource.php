<?php

namespace App\Http\Resources\Admin\Contact;

use App\Helpers\DateHelper;
use App\Http\Resources\Admin\MailLog\MailLogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'title'      => $this->title,
            'content'    => $this->content,
            'status'     => $this->status,
            'priority'   => $this->priority,
            'admin_note' => $this->admin_note,
            'replied_at' => DateHelper::parseOnlyDate($this->replied_at),
            'ip_address' => $this->ip_address,
            'created_at' => DateHelper::parseOnlyDate($this->created_at),
            'updated_at' => DateHelper::parseOnlyDate($this->updated_at),
            'mail_logs'  => MailLogResource::collection($this->whenLoaded('mailLogs')),
        ];
    }
}
