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
