<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Company\Information\UpdateInfoRequest;
use App\Services\Company\AuthService;
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
        $this->middleware([$this->authMiddleware(), 'is-company'])->except(
            [
                'login',
                'register',
                'refresh',
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
        if ($request->get('token')) {
            $this->authService->verifyEmail($request->get('token'));
        }
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
        $company = $this->authService->me();
        return $this->sendSuccessResponse($company);
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
     * Method update
     *
     * @param UpdateInfoRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function update(UpdateInfoRequest $request): JsonResponse
    {
        $data = $this->authService->update($request->only($this->getFieldsUpdate()));
        return $this->sendResponse($data, 'update');
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

    /**
     * Method getFieldsUpdate
     *
     * @return array
     */
    public function getFieldsUpdate(): array
    {
        return [
            'logo',
            'cover_img',
            'name',
            'short_name',
            'mail_address',
            'telephone',
            'city_id',
            'address',
            'website',
            'description',
        ];
    }
}
