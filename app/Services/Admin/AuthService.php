<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Services\BaseService;
use App\Services\Auth\BaseAuthService;

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
        return auth('admin')->user();
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
            auth('admin'),
            $request,
            User::ROLE_ADMIN
        );
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(): void
    {
        // auth('admin')->invalidate();
        auth('admin')->logout();
    }
}
