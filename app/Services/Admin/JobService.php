<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\Job;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobService extends BaseService
{
    use JobTrait;

    protected $orderables = [
        'id' => 'id',
        'title' => 'title',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'created_at' => 'created_at',
    ];

    protected $searchables = ['title'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'type' => 'filterByType',
        'start_date' => 'filterByStartDate',
        'end_date' => 'filterByEndDate',
        'company_id' => 'filterByCompany',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Job::with(['company', 'city.parent', 'jobCategory.parent']);
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
            $job = Job::with(['company', 'city.parent', 'jobCategory.parent'])->find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job;
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
