<?php

namespace App\Services\Auth;

use App\Helpers\ResponseHelper;
use App\Jobs\SendMailAuth;
use App\Models\PasswordResetToken;
use App\Models\RefreshToken;
use Exception;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Exceptions\JWTException;

class BaseAuthService
{
    private const REFRESH_TOKEN_DAYS = 7;
    private const REFRESH_TOKEN_COOKIE = 'refresh_token';

    public static function getInstance()
    {
        return app(static::class);
    }

    /**
     * Method login
     *
     * @param mixed $auth [explicite description]
     * @param array $request [explicite description]
     *
     * @return array
     */
    public function login(mixed $auth, array $request): array
    {
        try {
            $user = User::where('mail_address', $request['mail_address'])->first();

            if (!$user || !Hash::check($request['password'], $user->password)) {
                return ResponseHelper::sendError(
                    trans('auth.login_failed'),
                    ResponseHelper::STATUS_CODE_VALIDATE_ERROR
                );
            }

            if ($user->status === User::STATUS_UNVERIFIED) {
                return ResponseHelper::sendError(
                    trans('auth.not_active'),
                    ResponseHelper::STATUS_CODE_VALIDATE_ERROR
                );
            }

            if ($user->status === User::STATUS_LOCKED) {
                return ResponseHelper::sendError(
                    trans('auth.locked'),
                    ResponseHelper::STATUS_CODE_VALIDATE_ERROR
                );
            }

            $accessToken = $auth->login($user);
            $result = $this->respondWithToken($accessToken, $auth);
            $result['refresh_cookie'] = $this->issueRefreshCookie($user->id);
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method refreshToken
     *
     * @param ?string $rawToken [explicite description]
     * @param mixed $auth [explicite description]
     *
     * @return array
     */
    public function refreshToken(?string $rawToken, mixed $auth): array
    {
        if (!$rawToken) {
            return ResponseHelper::sendError(trans('auth.token_no_cookie'), ResponseHelper::STATUS_CODE_UNAUTHORIZED);
        }

        $record = RefreshToken::where('token', $this->hashToken($rawToken))
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return ResponseHelper::sendError(trans('auth.token_expired'), ResponseHelper::STATUS_CODE_UNAUTHORIZED);
        }

        $user = User::find($record->user_id);
        if (!$user || $user->status !== User::STATUS_ACTIVE) {
            return ResponseHelper::sendError(trans('auth.account_invalid'), ResponseHelper::STATUS_CODE_UNAUTHORIZED);
        }

        $accessToken = $auth->login($user);
        $result = $this->respondWithToken($accessToken, $auth);
        $result['refresh_cookie'] = $this->issueRefreshCookie($user->id);
        return $result;
    }

    /**
     * Method logout
     *
     * @param ?string $rawToken [explicite description]
     * @param mixed $auth [explicite description]
     *
     * @return Cookie
     */
    public function logout(?string $rawToken, mixed $auth): Cookie
    {
        $auth->logout();

        if ($rawToken) {
            RefreshToken::where('token', $this->hashToken($rawToken))->delete();
        }

        return $this->clearRefreshCookie();
    }

    /**
     * Method changePassword
     *
     * @param mixed $auth [explicite description]
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function changePassword(mixed $auth, array $data): array|bool
    {
        try {
            $user = User::find($auth->user()?->id);
            if (!Hash::check($data['old_password'], $user->password)) {
                return ['message' => trans('auth.password_incorrect')];
            }

            $user->update(['password' => $data['password']]);

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method forgotPassword
     *
     * @param string $email [explicite description]
     *
     * @return array
     */
    public function forgotPassword(string $email): array|bool
    {
        try {
            $user = User::where('mail_address', $email)->first();
            if (!$user) {
                return true;
            }

            DB::beginTransaction();
            $token = Str::random(config('length.token'));
            PasswordResetToken::where('mail_address', $email)->delete();
            PasswordResetToken::insert([
                'mail_address' => $email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            SendMailAuth::dispatch('forgot-password', [
                'mail_address' => $email,
                'token' => $token,
            ]);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method resetPassword
     *
     * @param string $token [explicite description]
     * @param array $data [explicite description]
     *
     * @return array
     */
    public function resetPassword(string $token, array $data): array|bool
    {
        try {
            DB::beginTransaction();
            $record = PasswordResetToken::where('mail_address', $data['mail_address'])->first();
            if (
                !$record ||
                !Hash::check($token, $record?->token) ||
                Carbon::parse($record?->created_at)->addMinutes(config('length.expire_time_reset_password'))->isPast()
            ) {
                return ResponseHelper::notFound(trans('auth.reset_password_failed'));
            }

            User::where('mail_address', $data['mail_address'])
                ->update(['password' => Hash::make($data['password'])]);
            PasswordResetToken::where('mail_address', $data['mail_address'])->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method register
     *
     * @param array $data [explicite description]
     * @param int $role [explicite description]
     *
     * @return bool
     */
    public function register(array $data, int $role): bool|array
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'mail_address' => $data['mail_address'],
                'password' => Hash::make($data['password']),
                'role' => $role,
                'status' => User::STATUS_UNVERIFIED,
            ]);

            if ($user) {
                $verifyUrl = URL::temporarySignedRoute(
                    'api.auth.verify_email',
                    now()->addMinutes(config('length.expire_time_verify_email')),
                    ['id' => $user->id]
                );

                SendMailAuth::dispatch('register-account', [
                    'mail_address' => $user->mail_address,
                    'verify_url'   => $verifyUrl,
                ]);
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
     * @param int $userId [explicite description]
     *
     * @return bool
     */
    public function verifyAccount(int $userId): bool
    {
        try {
            return (bool) User::query()
                ->where('id', $userId)
                ->where('status', User::STATUS_UNVERIFIED)
                ->update([
                    'status'            => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method updateEmail
     *
     * @param string $mail_address [explicite description]
     * @param int $id [explicite description]
     *
     * @return bool
     */
    public function updateEmail(string $mail_address, int $id): bool|array
    {
        return User::find($id)->update([
            'mail_address' => $mail_address,
        ]);
    }

    /**
     * Method me
     *
     * @param mixed $auth [explicite description]
     *
     * @return User
     */
    public function me(mixed $auth): User|null
    {
        $user = $auth->user();
        if ($user && $user->role !== User::ROLE_ADMIN) {
            $user->load($user->role === User::ROLE_COMPANY ? 'company' : 'applicant');
        }
        return $user;
    }

    /**
     * Method respondWithToken
     *
     * @param string $token [explicite description]
     * @param mixed $auth [explicite description]
     *
     * @return array
     */
    protected function respondWithToken(string $token, mixed $auth): array
    {
        $user = $auth->user();

        if ($user && $user->role !== User::ROLE_ADMIN) {
            $user->load($user->role === User::ROLE_COMPANY ? 'company' : 'applicant');
        }

        $roleString = match ($user?->role) {
            User::ROLE_ADMIN => 'admin',
            User::ROLE_COMPANY => 'company',
            User::ROLE_APPLICANT => 'applicant',
            default => 'unknown',
        };

        return [
            'me' => $user,
            'role' => $roleString,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60,
        ];
    }

    // ── Refresh token helpers ────────────────────────────────────────────────

    private function hashToken(string $rawToken): string
    {
        return hash('sha256', $rawToken);
    }

    /**
     * Method issueRefreshCookie
     *
     * @param int $userId [explicite description]
     *
     * @return Cookie
     */
    private function issueRefreshCookie(int $userId): Cookie
    {
        $rawToken = Str::random(64);
        RefreshToken::where('user_id', $userId)->delete();
        RefreshToken::create([
            'user_id'    => $userId,
            'token'      => $this->hashToken($rawToken),
            'expires_at' => now()->addDays(self::REFRESH_TOKEN_DAYS),
        ]);

        return cookie(
            self::REFRESH_TOKEN_COOKIE,
            $rawToken,
            60 * 24 * self::REFRESH_TOKEN_DAYS,
            '/',
            null,
            config('app.env') === 'production',
            true,
            false,
            'Lax'
        );
    }

    private function clearRefreshCookie(): Cookie
    {
        return cookie(
            self::REFRESH_TOKEN_COOKIE,
            '',
            -1,
            '/',
            null,
            config('app.env') === 'production',
            true,
            false,
            'Lax'
        );
    }
}
