<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\Company;
use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class CompanyService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'name' => 'name',
    ];

    protected $searchables = [
        'name',
        'short_name',
        'telephone',
        'description'
    ];

    protected $filterables = [
        'status' => 'filterByStatus',
    ];

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

        return $query->whereRelation('user', 'status', +$filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Company::with('user');
    }

    /**
     * Method detail
     *
     * @param int $id
     *
     * @return Company
     */
    public function detail(int $id): Company | array
    {
        try {
            $company = Company::with('user')->find($id);
            if (!$company) {
                return ResponseHelper::notFound('Company not found');
            }

            return $company;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method update
     *
     * @param array $data
     * @param int $id
     *
     * @return bool | array
     */
    public function update(array $data, int $id): bool | array
    {
        try {
            DB::beginTransaction();
            $company = Company::find($id);
            if (!$company) {
                return ResponseHelper::notFound('Company not found');
            }

            User::find($company->user_id)->update([
                'status' => $data['status'],
                'mail_address' => $data['mail_address'],
            ]);
            $company->update($data);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
