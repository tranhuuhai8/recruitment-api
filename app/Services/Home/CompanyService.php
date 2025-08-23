<?php

namespace App\Services\Home;

use App\Models\Company;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Log;

class CompanyService extends BaseService
{
    protected $searchables = ['name', 'short_name', 'telephone'];

    protected $filterables = [
        'city_id' => 'filterByCity',
    ];

    /**
     * filterByCity
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByCity(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        $data = (array) json_decode($filter['data'], true);
        if (!count($data)) {
            return $query;
        }

        return $query->whereIn('city_id', $data)
            ->orWhereHas('city', function ($sub) use ($data) {
                $sub->whereIn('parent_id', $data);
            });
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Company::query()
            ->with('city.parent')
            ->withCount('jobs')
            ->whereRelation('user', 'status', User::STATUS_ACTIVE)
            ->orderByDesc('jobs_count')
            ->take(Company::TOP_COMPANY);
    }
}
