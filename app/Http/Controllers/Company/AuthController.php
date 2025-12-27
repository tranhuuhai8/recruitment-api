<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Company\Information\UpdateInfoRequest;
use App\Services\Company\AuthService;
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
     *
     * @OA\Post(
     *     path="/api/auth/company/login",
     *     summary="Đăng nhập company",
     *     description="API đăng nhập cho tài khoản company",
     *     tags={"Company Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password"},
     *             @OA\Property(property="mail_address", type="string", format="email", example="company@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456Hh@"),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="",
     *                 description="Token verify email (tùy chọn)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Đăng nhập thành công"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="me", type="object"),
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string",
     *                     example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     *                 ),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
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
     *     path="/api/auth/company/refresh",
     *     summary="Làm mới token company",
     *     tags={"Company Authentication"},
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
     *     path="/api/auth/company/register",
     *     summary="Đăng ký company",
     *     description="API đăng ký tài khoản company mới",
     *     tags={"Company Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password", "password_confirmation"},
     *             @OA\Property(property="mail_address", type="string", format="email", example="company@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456Hh@"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123456Hh@")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đăng ký thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Đăng ký thành công"),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     ),
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
     *     path="/api/auth/company/me",
     *     summary="Lấy thông tin company hiện tại",
     *     tags={"Company Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/company/change-password",
     *     summary="Đổi mật khẩu company",
     *     tags={"Company Authentication"},
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
     *     path="/api/auth/company/logout",
     *     summary="Đăng xuất company",
     *     tags={"Company Authentication"},
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
