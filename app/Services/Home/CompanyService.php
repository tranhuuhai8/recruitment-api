<?php

namespace App\Services\Home;

use App\Helpers\ResponseHelper;
use App\Models\Company;
use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CompanyService extends BaseService
{
    use HomeTrait;

    protected $searchables = ['name', 'short_name', 'telephone'];

    protected $filterables = [
        'city_id' => 'filterByCity',
    ];

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

    /**
     * Method detail
     *
     * @param int $id [explicite description]
     *
     * @return Company | array
     */
    public function detail(int $id): Company|array
    {
        try {
            $company = Company::with('user')
                ->withCount('jobs')
                ->find($id);
            if (!$company) {
                return ResponseHelper::notFound();
            }

            return $company;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
