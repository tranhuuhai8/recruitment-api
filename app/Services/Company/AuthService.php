<?php

namespace App\Services\Company;

use App\Services\Auth\BaseAuthService;
use App\Models\User;
use App\Services\BaseService;

class AuthService extends BaseService
{
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
        return auth('company')->user();
    }

    /**
     * attemptLogin
     *
     * @param  array $request
     * @return array
     */
    public function attemptLogin(array $request): array
    {
        return BaseAuthService::getInstance()->login(
            auth('company'),
            $request,
            User::ROLE_COMPANY
        );
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
        return BaseAuthService::getInstance()->register(
            $request,
            User::ROLE_COMPANY,
        );
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(): void
    {
        // auth('company')->invalidate();
        auth('company')->logout();
    }
}
