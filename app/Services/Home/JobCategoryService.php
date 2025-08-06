<?php

namespace App\Services\Home;

use App\Models\JobCategory;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
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
    public function makeNewQuery(): Eloquent | QueryBuilder
    {
        return JobCategory::query()->where('status', JobCategory::STATUS_SHOW);
    }
}
