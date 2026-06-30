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
     * A company-role user only gets a `companies` row (and therefore a
     * slug) once they save their profile for the first time. Until then,
     * admins still need to be able to open that user to fill it in, so a
     * purely-numeric "slug" is treated as a fallback user id.
     *
     * @param string $slug
     *
     * @return Company | User | array
     */
    public function detail(string $slug): Company|User|array
    {
        try {
            $company = Company::with('user')->where('slug', $slug)->first();
            if ($company) {
                return $company;
            }

            if (ctype_digit($slug)) {
                $user = User::where('role', User::ROLE_COMPANY)->find($slug);
                if ($user) {
                    return $user;
                }
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
     * @param string $slug
     *
     * @return bool | array
     */
    public function update(array $data, string $slug): bool|array
    {
        try {
            DB::beginTransaction();
            $company = Company::where('slug', $slug)->first();
            $userId = $company?->user_id;

            if (!$company && ctype_digit($slug)) {
                $userId = (int) $slug;
            }

            $user = $userId ? User::where('role', User::ROLE_COMPANY)->find($userId) : null;
            if (!$user) {
                return ResponseHelper::notFound();
            }

            $user->update([
                'status' => $data['status'],
                'mail_address' => $data['mail_address'],
            ]);

            if ($company) {
                $company->update($this->makeDataUpdate($data));
            } else {
                Company::create($this->makeDataUpdate($data) + ['user_id' => $userId]);
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
