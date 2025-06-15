<?php

namespace App\Services\Applicant;

use App\Services\Auth\BaseAuthService;
use App\Models\User;
use App\Services\BaseService;

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
        return $this->auth->user();
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
