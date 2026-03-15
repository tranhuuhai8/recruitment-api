<?php

namespace App\Services\Auth;

use App\Models\User;
use Exception;
use App\Services\Auth\BaseAuthService;

class UnifiedAuthService
{
    /**
     * @param array $credentials
     * @return array
     * @throws Exception
     */
    public function login(array $credentials): array
    {
        $user = User::where('mail_address', $credentials['mail_address'])
                    ->first();

        if (!$user) {
            throw new Exception('Email hoặc mật khẩu không đúng.');
        }

        $validRoles = [User::ROLE_ADMIN, User::ROLE_COMPANY, User::ROLE_APPLICANT];
        if (!in_array($user->role, $validRoles, strict: true)) {
            throw new Exception('Tài khoản không hợp lệ.');
        }

        if ($user->status === User::STATUS_UNVERIFIED) {
            throw new Exception(trans('auth.not_active'));
        }

        if ($user->status === User::STATUS_LOCKED) {
            throw new Exception(trans('auth.locked'));
        }

        $token = auth('api')->attempt([
            'mail_address' => $credentials['mail_address'],
            'password' => $credentials['password'],
        ]);

        if (!$token) {
            throw new Exception('Email hoặc mật khẩu không đúng.');
        }

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => (int) auth('api')->factory()->getTTL() * 60,
            'role'       => $this->getRoleString($user->role),
            'user'       => $user,
        ];
    }

    public function getRoleString(int $role): string
    {
        return match ($role) {
            User::ROLE_ADMIN => 'admin',
            User::ROLE_COMPANY => 'company',
            User::ROLE_APPLICANT => 'applicant',
            default => 'unknown',
        };
    }

    public function me(): User|null
    {
        $user = auth('api')->user();
        if ($user && $user->role !== User::ROLE_ADMIN) {
            $user->load($user->role === User::ROLE_COMPANY ? 'company' : 'applicant');
        }
        return $user;
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(): array
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => (int) auth('api')->factory()->getTTL() * 60,
            'role'       => $this->getRoleString($user->role ?? 0),
            'user'       => $this->me(),
        ];
    }

    /**
     * Method changePassword
     *
     * @param array $data [explicite description]
     *
     * @return array | bool
     */
    public function changePassword(array $data): array|bool
    {
        return BaseAuthService::getInstance()->changePassword(auth('api'), $data);
    }
}
