<?php

namespace App\Http\Resources\Admin\MasterData;

use Illuminate\Http\Resources\Json\ResourceCollection;

class JobCategoriesCollection extends ResourceCollection
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
        $baseData = [ 'data' => JobCategoriesResource::collection($paginator) ];

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
