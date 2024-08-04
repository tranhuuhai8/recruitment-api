<?php

namespace App\Services\Employer;

use App\Services\Auth\BaseAuthService;
use Exception;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;

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
        return auth('employer')->user();
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
            auth('employer'),
            $request,
            User::ROLE_EMPLOYER
        );
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(): void
    {
        // auth('employer')->invalidate();
        auth('employer')->logout();
    }
}
