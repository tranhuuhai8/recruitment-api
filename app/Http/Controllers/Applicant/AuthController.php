<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Requests\Applicant\Information\UpdateInfoRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Applicant\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

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
        $this->middleware([$this->authMiddleware(), 'is-applicant'])->except(
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
     *
     * @OA\Post(
     *     path="/api/auth/applicant/login",
     *     summary="Đăng nhập applicant",
     *     tags={"Applicant Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password"},
     *             @OA\Property(property="mail_address", type="string", format="email", example="applicant@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456Hh@"),
     *             @OA\Property(property="token", type="string", description="Token verify email (tùy chọn)")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=422, description="Lỗi xác thực"),
     *     @OA\Response(response=400, description="Lỗi validation")
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/applicant/refresh",
     *     summary="Làm mới token applicant",
     *     tags={"Applicant Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/applicant/register",
     *     summary="Đăng ký applicant",
     *     tags={"Applicant Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password", "password_confirmation"},
     *             @OA\Property(property="mail_address", type="string", format="email", example="applicant@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456Hh@"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123456Hh@")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=422, description="Lỗi validation")
     * )
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
     *
     * @OA\Get(
     *     path="/api/auth/applicant/me",
     *     summary="Lấy thông tin applicant hiện tại",
     *     tags={"Applicant Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(): JsonResponse
    {
        $applicant = $this->authService->me();
        return $this->sendSuccessResponse($applicant);
    }

    /**
     * Method changePassword
     *
     * @param ChangePasswordRequest $request [explicite description]
     *
     * @return void
     *
     * @OA\Post(
     *     path="/api/auth/applicant/change-password",
     *     summary="Đổi mật khẩu applicant",
     *     tags={"Applicant Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "password", "password_confirmation"},
     *             @OA\Property(property="old_password", type="string", format="password"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=400, description="Lỗi validation"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/applicant/logout",
     *     summary="Đăng xuất applicant",
     *     tags={"Applicant Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
            'name',
            'avatar',
            'gender',
            'birthday',
            'mail_address',
            'telephone',
            'address',
            'description',
        ];
    }
}
