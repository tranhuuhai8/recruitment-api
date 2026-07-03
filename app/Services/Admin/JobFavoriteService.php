<?php

namespace App\Services\Admin;

use App\Models\JobFavorite;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobFavoriteService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $filterables = [
        'job_id' => 'filterByJobId',
    ];

    /**
     * filterByJobId
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByJobId(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('job_id', +$filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return JobFavorite::query()
            ->with(['applicant.user', 'job'])
            ->orderByDesc('id');
    }
}
