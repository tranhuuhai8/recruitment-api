<?php

namespace App\Services\Auth;

use App\Jobs\SendMailAuth;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
     * @param array $request
     * @param int $role
     *
     * @return array
     */
    public function login($auth, array $request, int $role): array
    {
        try {
            $user = User::where('mail_address', $request['mail_address'])->first();
            $password = $request['password'];

            if (!$user || $user->role !== $role || !Hash::check($password, $user->password)) {
                return ['message' => trans('auth.login_failed')];
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
     * Method register
     *
     * @param array $data
     * @param int $role
     *
     * @return bool | array
     */
    public function register(array $data, int $role): bool | array
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'mail_address' => $data['mail_address'],
                'password' => Hash::make($data['password']),
                'role' => $role,
                'status' => User::STATUS_INACTIVE,
                'token_verify' => Hash::make($data['mail_address']),
            ]);

            if ($user) {
                SendMailAuth::dispatch('register-account', $user);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method verifyAccount
     *
     * @param string $token [explicite description]
     *
     * @return bool
     */
    public function verifyAccount(string $token): bool | array
    {
        try {
            return User::query()
            ->where('token_verify', $token)
            ->update([
                'status' => User::STATUS_ACTIVE,
                'token_verify' => null,
                'email_verified_at' => now(),
            ]);
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
