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
                return ResponseHelper::sendError(trans('JobCategory not found!'));
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
                return ResponseHelper::sendError(trans('JobCategory not found!'));
            }

            return $jobCategory->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
