<?php

namespace App\Http\Resources\Company\Job;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $city = $this->city;
        $jobCategory = $this->jobCategory;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => DateHelper::parseOnlyDate($this->start_date),
            'end_date' => DateHelper::parseOnlyDate($this->end_date),
            'city_name' => $this->city->name,
            'city_parent_name' => $city->parent ? " (" . $city->parent->name . ')' : '',
            'job_category_name' => $this->jobCategory->name,
            'job_category_parent_name' => $jobCategory->parent ? " (" . $jobCategory->parent->name . ')' : '',
        ];
    }
}
