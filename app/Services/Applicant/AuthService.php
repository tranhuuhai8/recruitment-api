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
        $this->auth = auth('applicant');
    }

    public function makeNewQuery()
    {
        return User::query();
    }

    /**
     * me
     *
     * @return User | null
     */
    public function me(): User|null
    {
        return User::query()
            ->with(['applicant'])
            ->where('id', $this->auth->id())
            ->first();
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
     * attemptLogin
     *
     * @param  $request
     * @return array
     */
    public function attemptLogin($request): array
    {
        return BaseAuthService::getInstance()->login($this->auth, $request, User::ROLE_APPLICANT);
    }

    /**
     * Method changePassword
     *
     * @param array $data [explicite description]
     *
     * @return array | bool
     */
    public function changePassword(array $data): array | bool
    {
        return BaseAuthService::getInstance()->changePassword($this->auth, $data);
    }

    /**
     * Method verifyEmail
     *
     * @param string $token
     *
     * @return bool | array
     */
    public function verifyEmail(string $token): bool|array
    {
        return BaseAuthService::getInstance()->verifyAccount($token);
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

    /**
     * Method logout
     *
     * @return void
     */
    public function logout(): void
    {
        // $this->auth->invalidate();
        $this->auth->logout();
    }

    /**
     * Method refreshToken
     *
     * @return array
     */
    public function refreshToken(): array
    {
        return BaseAuthService::getInstance()->refreshToken($this->auth);
    }
}
