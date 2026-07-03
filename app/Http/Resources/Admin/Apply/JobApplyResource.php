<?php

namespace App\Http\Resources\Admin\Apply;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_id' => $this->job_id,
            'job_title' => $this->job?->title,
            'applicant_id' => $this->applicant_id,
            'full_name' => $this->applicant?->name ?? $this->guest_name,
            'email' => $this->applicant?->user?->mail_address ?? $this->guest_email,
            'telephone' => $this->applicant?->telephone ?? $this->guest_telephone,
            'file_name' => $this->applicationFile?->file_name ?? $this->file_name,
            'file_path' => $this->applicationFile?->file_path ?? $this->file_path,
            'status' => $this->status,
            'cover_letter' => $this->cover_letter,
            'created_at' => DateHelper::parseDateBe($this->created_at),
        ];
    }
}
