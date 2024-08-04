<?php

namespace App\Services\Candidate;

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
        return auth('candidate')->user();
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
            auth('candidate'),
            $request,
            User::ROLE_CANDIDATE
        );
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(): void
    {
        // auth('candidate')->invalidate();
        auth('candidate')->logout();
    }
}
