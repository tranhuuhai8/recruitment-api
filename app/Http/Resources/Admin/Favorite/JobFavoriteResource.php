<?php

namespace App\Http\Resources\Admin\Favorite;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobFavoriteResource extends JsonResource
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
            'applicant_id' => $this->applicant_id,
            'full_name' => $this->applicant?->name,
            'email' => $this->applicant?->user?->mail_address,
            'telephone' => $this->applicant?->telephone,
            'created_at' => DateHelper::parseDateBe($this->created_at),
        ];
    }
}
