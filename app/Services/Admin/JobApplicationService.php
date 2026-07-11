<?php

namespace App\Services\Admin;

use App\Models\JobApplication;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobApplicationService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $filterables = [
        'job_id' => 'filterByJobId',
        'applicant_id' => 'filterByApplicantId',
        'status' => 'filterByStatus',
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
     * filterByApplicantId
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByApplicantId(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('applicant_id', +$filter['data']);
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
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return JobApplication::query()
            ->with(['applicant.user', 'applicationFile', 'job.company'])
            ->whereRelation('job', 'deleted_at', null);
    }
}
