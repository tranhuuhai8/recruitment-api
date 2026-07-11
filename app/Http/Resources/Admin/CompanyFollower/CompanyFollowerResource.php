<?php

namespace App\Http\Resources\Admin\CompanyFollower;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyFollowerResource extends JsonResource
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
            'applicant_id' => $this->applicant_id,
            'full_name' => $this->applicant?->name,
            'applicant_user_id' => $this->applicant?->user_id,
            'email' => $this->applicant?->user?->mail_address,
            'telephone' => $this->applicant?->telephone,
            'company_id' => $this->company_id,
            'company_name' => $this->company?->name,
            'company_user_id' => $this->company?->user_id,
            'company_logo' => $this->company?->logo,
            'notify_new_job' => (bool) $this->notify_new_job,
            'created_at' => DateHelper::parseDateBe($this->created_at),
        ];
    }
}
