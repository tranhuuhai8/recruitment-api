<?php

namespace App\Http\Resources\Admin\Contact;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'status'     => $this->status,
            'priority'   => $this->priority,
            'replied_at' => DateHelper::parseOnlyDate($this->replied_at),
            'created_at' => DateHelper::parseOnlyDate($this->created_at),
        ];
    }
}
