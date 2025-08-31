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
        'parent_id' => 'filterByParent',
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
     * Method filterByParent
     *
     * @param Eloquent $query [explicite description]
     * @param array $filter [explicite description]
     *
     * @return Eloquent
     */
    public function filterByParent(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        $data = (array) json_decode($filter['data'], true);
        if (!count($data)) {
            return $query;
        }

        return $query->whereIn('parent_id', $data);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent | QueryBuilder
    {
        return JobCategory::with('parent');
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
            if ($this->validateCategoryName($data)) {
                return ResponseHelper::sendError(trans('response.data_exist'));
            }

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

            if ($this->validateCategoryName($data, $id)) {
                return ResponseHelper::sendError(trans('response.data_exist'));
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

    /**
     * Method validateCategoryName
     *
     * @param array $data [explicite description]
     * @param $id $id [explicite description]
     *
     * @return bool
     */
    public function validateCategoryName(array $data, $id = null): bool
    {
        $name = mb_strtolower($data['name']);
        $parentId = $data['parent_id'] ?? null;

        return JobCategory::query()
            ->whereRaw('LOWER(name) = ?', [$name])
            ->when($parentId, function ($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            }, function ($q) {
                $q->whereNull('parent_id');
            })
            ->when($id, function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->exists();
    }
}
