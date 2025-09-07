<?php

namespace App\Services\Company;

use App\Models\Job;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobService extends BaseService
{
    use CompanyTrait;

    protected $orderables = [
        'id' => 'id',
    ];

    protected $searchables = ['name'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'type' => 'filterByType',
        'city_id' => 'filterByCity',
        'job_category_id' => 'filterByJobCategory',
        'number_of_recruitment' => 'filterByNumberOfRecruitment',
        'start_date' => 'filterByStartDate',
        'end_date' => 'filterByEndDate',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Job::query()
            ->with(['city.parent', 'jobCategory.parent'])
            ->whereRelation('company', 'user_id', auth('company')->id());
    }
}
