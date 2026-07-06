<?php

namespace App\Http\Resources\Home\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $city = $this->city;
        $cityParent = $city->parent ? ' ( ' . $city->parent->name . ' )' : '';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'logo' => $this->logo,
            'city_name' => $this->city->name . $cityParent,
            'jobs_count' => $this->jobs_count,
            'followers_count' => $this->followers_count,
        ];
    }
}
