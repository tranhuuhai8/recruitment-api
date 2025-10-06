<?php

namespace App\Http\Resources\Home\Job;

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
            'title' => $this->title,
            'type' => $this->type,
            'company_logo' => $this->company?->logo,
            'company_name' => $this->company?->name,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'city_name' => $city->parent ? $city->parent->name : $city->name,
            'category_name' => $jobCategory->parent ? $jobCategory->parent->name : $jobCategory->name,
        ];
    }
}
