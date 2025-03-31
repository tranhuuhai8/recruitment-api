<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\Candidate\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware($this->authMiddleware())->except([
            'login',
        ]);
    }

    /**
     * login
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = AuthService::getInstance()->attemptLogin($request);
        return $this->sendResponse($data, '', trans('auth.login_success'));
    }

    /**
     * me
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $candidate = AuthService::getInstance()->me();
        return $this->sendSuccessResponse($candidate);
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
}
