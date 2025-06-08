<?php

namespace App\Services\Applicant;

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
        return auth('applicant')->user();
    }

    /**
     * attemptLogin
     *
     * @param  $request
     * @return array
     */
    public function attemptLogin($request): array
    {
        return BaseAuthService::getInstance()->login(
            auth('applicant'),
            $request,
            User::ROLE_APPLICANT
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
            User::ROLE_APPLICANT,
        );
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(): void
    {
        // auth('applicant')->invalidate();
        auth('applicant')->logout();
    }
}
