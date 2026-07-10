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
        'telephone' => 'telephone',
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
        return User::query()
            ->leftJoin('companies', 'users.id', 'companies.user_id')
            ->where('users.role', User::ROLE_COMPANY)
            ->orderBy('users.status')
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
            'companies.id as company_id',
            'companies.slug',
            'companies.name',
            'companies.short_name',
            'companies.telephone',
            'companies.logo',
        ]);
    }

    /**
     * Method detail
     *
     * A company-role user only gets a `companies` row once they save their
     * profile for the first time. Until then, admins still need to be able
     * to open that user to fill it in, so we fall back to the bare user.
     *
     * @param int $id
     *
     * @return Company | User | array
     */
    public function detail(int $id): Company|User|array
    {
        try {
            $company = Company::with(['user', 'city'])
                ->withCount(['jobs', 'followers'])
                ->where('user_id', $id)
                ->first();
            if ($company) {
                return $company;
            }

            $user = User::where('role', User::ROLE_COMPANY)->find($id);
            if ($user) {
                return $user;
            }

            return ResponseHelper::notFound();
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
            $user = User::where('role', User::ROLE_COMPANY)->find($id);
            if (!$user) {
                return ResponseHelper::notFound();
            }

            $user->update([
                'status' => $data['status'],
                'mail_address' => $data['mail_address'],
            ]);

            $company = Company::where('user_id', $id)->first();
            if ($company) {
                $company->update($this->makeDataUpdate($data));
            } else {
                Company::create($this->makeDataUpdate($data) + ['user_id' => $id]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method makeDataUpdate
     *
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function makeDataUpdate(array $data): array
    {
        return [
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
