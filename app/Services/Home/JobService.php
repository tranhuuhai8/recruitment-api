<?php

namespace App\Services\Home;

use App\Helpers\ResponseHelper;
use App\Models\Job;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobService extends BaseService
{
    use HomeTrait;

    protected $searchables = ['title'];

    protected $filterables = [
        'city_id' => 'filterByCity',
        'company_id' => 'filterByCompany',
        'job_category_id' => 'filterByJobCategory',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Job::with(['company', 'city.parent', 'jobCategory.parent'])
            ->where('status', Job::STATUS_OPEN)
            ->orderByDesc('end_date');
    }

    /**
     * Method detail
     *
     * @param int $id [explicite description]
     *
     * @return Job | array
     */
    public function detail(int $id): Job|array
    {
        try {
            $job = Job::with(['company.city', 'company.user', 'city.parent', 'jobCategory.parent'])
                ->where('status', Job::STATUS_OPEN)
                ->find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
