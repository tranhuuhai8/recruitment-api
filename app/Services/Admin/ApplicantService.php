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
        'telephone' => 'telephone',
    ];

    protected $searchables = [
        'name' => 'applicants.name',
        'telephone' => 'applicants.telephone',
        'mail_address' => 'users.mail_address',
    ];

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

        return $query->where('status', +$filter['data']);
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
        return User::query()
            ->leftJoin('applicants', 'users.id', 'applicants.user_id')
            ->where('users.role', User::ROLE_APPLICANT)
            ->orderBy('users.status')
            ->selectRaw($this->getSelectRaw());
    }

    /**
     * Method getSelectRaw
     *
     * @return string
     */
    protected function getSelectRaw(): string
    {
        return implode(', ', [
            'users.id',
            'users.status',
            'users.mail_address',
            'applicants.avatar',
            'applicants.name',
            'applicants.gender',
            'applicants.telephone',
            'applicants.birthday',
            'applicants.address',
        ]);
    }

    /**
     * Method detail
     *
     * @param int $id [explicite description]
     *
     * @return Applicant | User | array
     */
    public function detail(int $id): Applicant|User|array
    {
        try {
            $applicant = Applicant::with('user')->where('user_id', $id)->first() ?: User::find($id);
            if (!$applicant) {
                return ResponseHelper::notFound();
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
    public function update(array $data, int $id): bool|array
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::notFound();
            }

            User::find($id)->update([
                'status' => $data['status'],
                'mail_address' => $data['mail_address'],
            ]);
            Applicant::upsert(
                $this->makeDataUpsert($id, $data),
                ['user_id'],
                ['name', 'avatar', 'gender', 'birthday', 'telephone', 'address', 'description']
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method makeDataUpsert
     *
     * @param int $id [explicite description]
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function makeDataUpsert(int $id, array $data): array
    {
        return [
            'user_id' => $id,
            'name' => data_get($data, 'name'),
            'avatar' => data_get($data, 'avatar'),
            'gender' => data_get($data, 'gender'),
            'birthday' => data_get($data, 'birthday'),
            'address' => data_get($data, 'address'),
            'telephone' => data_get($data, 'telephone'),
            'description' => data_get($data, 'description'),
        ];
    }
}
