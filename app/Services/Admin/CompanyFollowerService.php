<?php

namespace App\Services\Admin;

use App\Models\CompanyFollower;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CompanyFollowerService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $filterables = [
        'company_id' => 'filterByCompanyId',
        'applicant_id' => 'filterByApplicantId',
    ];

    /**
     * filterByCompanyId
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByCompanyId(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('company_id', +$filter['data']);
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
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return CompanyFollower::query()
            ->with(['applicant.user', 'company'])
            ->whereHas('applicant.user', function ($query) {
                $query->where('status', User::STATUS_ACTIVE)
                    ->whereNull('deleted_at');
            })
            ->orderByDesc('id');
    }
}
