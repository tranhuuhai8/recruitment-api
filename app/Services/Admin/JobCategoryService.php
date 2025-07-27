<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\JobCategory;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobCategoryService extends BaseService
{
    protected $orderables = [
        'name' => 'name',
    ];

    protected $searchables = ['name', 'description'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'type' => 'filterByType',
    ];

    /**
     * filterByType
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByType(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('type', +$filter['data']);
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
    public function makeNewQuery(): Eloquent | QueryBuilder
    {
        return JobCategory::query();
    }

    /**
     * store
     *
     * @param  array $data
     * @return JobCategory | array
     */
    public function store(array $data): JobCategory | array
    {
        try {
            return JobCategory::create($data);
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
    public function update(array $data, int $id): bool | array
    {
        try {
            $jobCategory = JobCategory::find($id);
            if (!$jobCategory) {
                return ResponseHelper::notFound();
            }

            return $jobCategory->update($data);
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
    public function delete(int $id): bool | array
    {
        try {
            $jobCategory = JobCategory::find($id);
            if (!$jobCategory) {
                return ResponseHelper::notFound();
            }

            return $jobCategory->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
