<?php

namespace App\Services\Company;

use App\Helpers\ResponseHelper;
use App\Models\Company;
use App\Services\Auth\BaseAuthService;
use App\Models\User;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthService extends BaseService
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth('api');
    }

    public function makeNewQuery()
    {
        return User::query();
    }

    /**
     * Method update
     *
     * @param array $data [explicite description]
     *
     * @return bool
     */
    public function update(array $data): bool|array
    {
        try {
            DB::beginTransaction();
            $user = User::find($this->auth->id());

            if (!$user) {
                return ResponseHelper::notFound();
            }
            BaseAuthService::getInstance()->updateEmail($data['mail_address'], $user->id);
            Company::upsert(
                $this->mapDataCompany($data),
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
     * Method mapDataCompany
     *
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function mapDataCompany(array $data): array
    {
        return [
            'user_id' => $this->auth->id(),
            'logo' => data_get($data, 'logo'),
            'cover_img' => data_get($data, 'cover_img'),
            'name' => data_get($data, 'name'),
            'short_name' => data_get($data, 'short_name'),
            'city_id' => data_get($data, 'city_id'),
            'website' => data_get($data, 'website'),
            'address' => data_get($data, 'address'),
            'telephone' => data_get($data, 'telephone'),
            'description' => data_get($data, 'description'),
        ];
    }



    /**
     * Method register
     *
     * @param array $request
     *
     * @return bool | array
     */
    public function register(array $request): bool|array
    {
        return BaseAuthService::getInstance()->register($request, User::ROLE_COMPANY);
    }
}
