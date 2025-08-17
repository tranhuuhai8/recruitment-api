<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    protected $authService;

    /**
     * Method __construct
     *
     * @param AuthService $authService [explicite description]
     *
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->middleware([$this->authMiddleware(), 'is-admin'])->except(
            [
                'login',
                'refresh'
            ]
        );
        $this->authService = $authService;
    }

    /**
     * login
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->attemptLogin($request->only($this->getFields()));
        return $this->sendResponse($data, '', trans('auth.login_success'));
    }

    /**
     * Method refresh
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->sendResponse($this->authService->refreshToken());
    }

    /**
     * me
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $admin = $this->authService->me();
        return $this->sendSuccessResponse($admin);
    }

    /**
     * Method changePassword
     *
     * @param ChangePasswordRequest $request [explicite description]
     *
     * @return void
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $this->authService->changePassword($request->only([
            'old_password',
            'password',
        ]));


        return $this->sendResponse($data);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->sendSuccessResponse(true, trans('auth.logout_success'));
    }

    /**
     * getFields
     *
     * @return array
     */
    public function getFields(): array
    {
        return [
            'mail_address',
            'password',
        ];
    }
}
