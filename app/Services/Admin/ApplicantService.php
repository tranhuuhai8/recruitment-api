<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\Applicant;
use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class ApplicantService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'name' => 'name',
    ];

    protected $searchables = ['name', 'telephone', 'description'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'gender' => 'filterByGender',
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
     * filterByGender
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByGender(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('gender', +$filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Applicant::with('user');
    }

    /**
     * Method detail
     *
     * @param int $id
     *
     * @return Applicant | array
     */
    public function detail(int $id): Applicant | array
    {
        try {
            $applicant = Applicant::with('user')->find($id);
            if (!$applicant) {
                return ResponseHelper::notFound('Applicant not found');
            }

            return $applicant;
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
            $applicant = Applicant::find($id);
            if (!$applicant) {
                return ResponseHelper::notFound('Applicant not found');
            }

            User::find($applicant->user_id)->update([
                'status' => $data['status'],
                'mail_address' => $data['mail_address'],
            ]);
            $applicant->update($data);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
