<?php

namespace App\Services\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BaseAuthService
{
    /**
     * Create new service instance
     *
     * @return $this
     */
    public static function getInstance()
    {
        return app(static::class);
    }

    /**
     * Method login
     *
     * @param $auth
     * @param $request
     * @param int $role
     *
     * @return array
     */
    public function login($auth, $request, int $role): array
    {
        try {
            $user = User::where('mail_address', $request->get('mail_address'))->first();
            $password = $request->get('password');

            if (!Hash::check($password, $user->password)) {
                return ['message' => trans('auth.login_failed')];
            }

            if (!$user || $user->role !== $role) {
                return ['message' => trans('auth.permission_denied')];
            }

            if ($user->status === User::STATUS_INACTIVE) {
                return ['message' => trans('auth.not_active')];
            }

            $token = $auth->login($user);
            return $this->respondWithToken($token, $auth);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * respondWithToken
     *
     * @param  $token
     * @param  $auth
     * @return array
     */
    protected function respondWithToken($token, $auth): array
    {
        return [
            'me' => $auth->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => auth('admin')->factory()->getTTL(),
        ];
    }
}
