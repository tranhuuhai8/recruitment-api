<?php

namespace App\Http\Resources\Admin\Applicant;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
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
            'created_at' => DateHelper::parseOnlyDate($this->created_at),
        ];
    }
}
