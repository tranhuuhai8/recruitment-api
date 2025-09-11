<?php

namespace App\Services\Company;

use App\Helpers\ResponseHelper;
use App\Models\Job;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobService extends BaseService
{
    use CompanyTrait;

    protected $orderables = [
        'title' => 'title',
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
            $job = Job::find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * store
     *
     * @param  array $data
     * @return Job | array
     */
    public function store(array $data): Job|array
    {
        try {
            return Job::create($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  array $data
     * @param  int $id
     * @return bool | array
     */
    public function update(array $data, int $id): bool|array
    {
        try {
            $job = Job::find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job->update($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * delete
     *
     * @param  int $id
     * @return bool | array
     */
    public function delete(int $id): bool|array
    {
        try {
            $job = Job::find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
