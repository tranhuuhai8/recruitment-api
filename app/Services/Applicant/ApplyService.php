<?php

namespace App\Services\Applicant;

use App\Models\JobApplication;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ApplyService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $searchables = [
        'title' => 'job.title',
        'name' => 'job.company.name',
    ];

    protected $searchableRelations = true;

    protected $filterables = [
        'job_id' => 'filterByJobId',
        'status' => 'filterByStatus',
        'created_at' => 'filterByDate',
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
     * filterByStatus
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByStatus(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('status', +$filter['data']);
    }

    /**
     * Method filterByDate
     *
     * @param Eloquent $query [explicite description]
     * @param array $filter [explicite description]
     *
     * @return Eloquent
     */
    public function filterByDate(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->whereDate('created_at', $filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return JobApplication::query()
            ->with(['job.company', 'applicationFile'])
            ->where('applicant_id', auth('applicant')->user()?->applicant?->id);
    }
}
