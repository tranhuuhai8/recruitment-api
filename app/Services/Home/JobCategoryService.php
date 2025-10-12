<?php

namespace App\Services\Home;

use App\Models\JobCategory;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobCategoryService extends BaseService
{
    protected $orderables = [
        'name' => 'name',
    ];

    protected $searchables = ['name'];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return JobCategory::query()->where('status', JobCategory::STATUS_SHOW);
    }

    /**
     * Method dataParent
     *
     * @param array $request [explicite description]
     *
     * @return Eloquent
     */
    public function dataParent(array $request): Collection
    {
        $this->query = JobCategory::query()
            ->select('job_categories.*')
            ->selectRaw('
                (
                    SELECT COUNT(*)
                    FROM jobs 
                    WHERE jobs.job_category_id = job_categories.id
                    OR jobs.job_category_id IN (
                            SELECT id FROM job_categories AS child 
                            WHERE child.parent_id = job_categories.id
                        )
                ) as total_jobs
            ')
            ->where([
                'status' => JobCategory::STATUS_SHOW,
                'parent_id' => null,
            ]);

        return $this->data(...$request);
    }
}
