<?php

namespace App\Http\Resources\Applicant\Favorite;

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
        $job = $this->job;

        return [
            'id' => $job?->id,
            'slug' => $job?->slug,
            'title' => $job?->title,
            'company_name' => $job?->company?->name,
            'company_slug' => $job?->company?->slug,
            'is_applied' => (bool) $job?->applications?->isNotEmpty(),
            'created_at' => DateHelper::parseDateBe($this->created_at),
        ];
    }
}
