<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware($this->authMiddleware())->except(
            [
            'login',
            ]
        );
    }

    /**
     * login
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = AuthService::getInstance()->attemptLogin($request->only($this->getFields()));
        return $this->sendResponse($data, '', trans('auth.login_success'));
    }

    /**
     * me
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $admin = AuthService::getInstance()->me();
        return $this->sendSuccessResponse($admin);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        AuthService::getInstance()->logout();
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
