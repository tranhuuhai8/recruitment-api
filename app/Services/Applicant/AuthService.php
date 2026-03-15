<?php

namespace App\Services\Applicant;

use App\Helpers\ResponseHelper;
use App\Models\Applicant;
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
     * @return bool|array
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
            Applicant::upsert(
                $this->mapDataApplicant($data),
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
     * Method mapDataApplicant
     *
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function mapDataApplicant(array $data): array
    {
        return [
            'user_id' => $this->auth->id(),
            'name' => data_get($data, 'name'),
            'avatar' => data_get($data, 'avatar'),
            'gender' => data_get($data, 'gender'),
            'birthday' => data_get($data, 'birthday'),
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
        return BaseAuthService::getInstance()->register($request, User::ROLE_APPLICANT);
    }
}
