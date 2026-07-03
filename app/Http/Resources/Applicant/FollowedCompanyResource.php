<?php

namespace App\Http\Resources\Applicant;

use App\Http\Resources\Home\Company\CompanyResource;
use Illuminate\Http\Request;

class FollowedCompanyResource extends CompanyResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'notify_new_job' => (bool) $this->notify_new_job,
        ];
    }
}
