<?php

namespace App\Http\Resources\Applicant\Apply;

use Illuminate\Http\Resources\Json\ResourceCollection;

class JobApplyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $paginator = $this->resource;
        $baseData = [ 'data' => JobApplyResource::collection($paginator) ];

        if (data_get($request, 'all')) {
            return $baseData;
        }
        return [
            ...$baseData,
            'per_page' => $paginator->perPage(),
            'total_page' => $paginator->lastPage(),
            'current_page' => $paginator->currentPage(),
            'total' => $paginator->total(),
        ];
    }
}
