<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\BaseService;
use App\Services\Auth\BaseAuthService;

class AuthService extends BaseService
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth('admin');
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
        return BaseAuthService::getInstance()->login($this->auth, $request, User::ROLE_ADMIN);
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
