<?php

namespace App\Http\Resources\Admin\MailTemplate;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'code'      => $this->code,
            'subject'   => $this->subject,
            'body'      => $this->body,
            'variables' => $this->variables,
            'type'      => $this->type,
            'is_active' => $this->is_active,
            'created_at' => DateHelper::parseOnlyDate($this->created_at),
            'updated_at' => DateHelper::parseOnlyDate($this->updated_at),
        ];
    }
}
