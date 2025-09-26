<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\Company;
use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class CompanyService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'name' => 'name',
    ];

    protected $searchables = [
        'name' => 'companies.name',
        'short_name' => 'companies.short_name',
        'telephone' => 'companies.telephone',
        'mail_address' => 'users.mail_address',
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

        return $query->where('status', +$filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return User::leftJoin('companies', 'users.id', 'companies.user_id')
            ->where('users.role', User::ROLE_COMPANY)
            ->selectRaw($this->getSelectRaw());
    }

    /**
     * Method listCompany
     *
     * @param array $request [explicite description]
     *
     * @return Collection
     */
    public function listCompany(array $request): Collection
    {
        $this->query = Company::query()
            ->whereRelation('user', 'status', User::STATUS_ACTIVE)
            ->selectRaw('id, name');

        return $this->data(...$request);
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
            'companies.name',
            'companies.short_name',
            'companies.telephone',
        ]);
    }

    /**
     * Method detail
     *
     * @param int $id
     *
     * @return Company | User | array
     */
    public function detail(int $id): Company|User|array
    {
        try {
            $company = Company::with('user')->where('user_id', $id)->first() ?: User::find($id);
            if (!$company) {
                return ResponseHelper::notFound();
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
            Company::upsert(
                $this->makeDataUpsert($id, $data),
                ['user_id'],
                ['logo', 'cover_img', 'name', 'short_name', 'city_id', 'website', 'telephone', 'address', 'description']
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
            'logo' => data_get($data, 'logo'),
            'cover_img' => data_get($data, 'cover_img'),
            'name' => data_get($data, 'name'),
            'short_name' => data_get($data, 'short_name'),
            'telephone' => data_get($data, 'telephone'),
            'city_id' => data_get($data, 'city_id'),
            'address' => data_get($data, 'address'),
            'website' => data_get($data, 'website'),
            'description' => data_get($data, 'description'),
        ];
    }
}
