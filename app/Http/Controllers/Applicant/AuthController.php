<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Applicant\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    protected $authService;

    /**
     * Method __construct
     *
     * @param AuthService $authService
     *
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware($this->authMiddleware())->except(
            [
                'login',
                'register'
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
        if ($request->get('token')) {
            $this->authService->verifyEmail($request->get('token'));
        }
        $data = $this->authService->attemptLogin($request->only($this->getFields()));
        return $this->sendResponse($data, '', trans('auth.login_success'));
    }

    /**
     * register
     *
     * @param  RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->only($this->getFields()));
        return $this->sendResponse($data, '', trans('auth.register_success'));
    }

    /**
     * me
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $applicant = $this->authService->me();
        return $this->sendSuccessResponse($applicant);
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
