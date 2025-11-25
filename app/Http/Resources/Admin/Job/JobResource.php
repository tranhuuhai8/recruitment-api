<?php

namespace App\Http\Resources\Admin\Job;

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
            'title' => $this->title,
            'type' => $this->type,
            'number_of_recruitment' => $this->number_of_recruitment,
            'status' => $this->status,
            'company' => $this->company,
            'company_id' => $this->company_id,
            'start_date' => DateHelper::parseOnlyDate($this->start_date),
            'end_date' => DateHelper::parseOnlyDate($this->end_date),
            'created_at' => DateHelper::parseOnlyDate($this->created_at),
            'city_name' => $this->city->name,
            'city_parent_name' => $city->parent ? " (" . $city->parent->name . ')' : '',
            'job_category_name' => $this->jobCategory->name,
            'job_category_parent_name' => $jobCategory->parent ? " (" . $jobCategory->parent->name . ')' : '',
        ];
    }
}
