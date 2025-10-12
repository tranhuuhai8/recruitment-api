<?php

namespace App\Services\Home;

use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait HomeTrait
{
    /**
     * filterByCity
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByCity(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        $data = (array) json_decode($filter['data'], true);
        if (!count($data)) {
            return $query;
        }

        return $query->whereIn('city_id', $data)
            ->orWhereHas('city', function ($sub) use ($data) {
                $sub->whereIn('parent_id', $data);
            });
    }

    /**
     * Method filterByJobCategory
     *
     * @param Eloquent $query [explicite description]
     * @param array $filter [explicite description]
     *
     * @return Eloquent
     */
    public function filterByJobCategory(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        $data = (array) json_decode($filter['data'], true);
        if (!count($data)) {
            return $query;
        }

        return $query->where(function ($q) use ($data) {
            $q->whereIn('job_category_id', $data)
                ->orWhereHas('jobCategory', function ($q2) use ($data) {
                    $q2->whereIn('parent_id', $data);
                });
        });
    }
}
