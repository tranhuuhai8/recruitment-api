<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

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
     *
     * @OA\Post(
     *     path="/api/auth/admin/login",
     *     summary="Đăng nhập admin",
     *     description="API đăng nhập cho tài khoản admin",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password"},
     *             @OA\Property(
     *                 property="mail_address",
     *                 type="string",
     *                 format="email",
     *                 example="admin@gmail.com",
     *                 description="Email"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="123456Hh@",
     *                 description="Mật khẩu"
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="",
     *                 description="Token (tùy chọn)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Đăng nhập thành công"),
     *             @OA\Property(property="errors", type="null", example=null),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="me",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="mail_address", type="string", example="admin@gmail.com"),
     *                     @OA\Property(
     *                         property="role",
     *                         type="integer",
     *                         example=1,
     *                         description="1: Admin, 2: Company, 3: Applicant"
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="integer",
     *                         example=1,
     *                         description="1: Active, 2: Unverified, 3: Locked"
     *                     ),
     *                     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string",
     *                     example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     *                 ),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="integer",
     *                     example=3600,
     *                     description="Thời gian hết hạn token (giây)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi xác thực",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Thông tin đăng nhập không chính xác"),
     *             @OA\Property(property="errors", type="null", example=null),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Lỗi request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=400),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Object chứa các lỗi validation, key là tên field, value là mảng các thông báo lỗi"
     *             ),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/admin/refresh",
     *     summary="Làm mới token admin",
     *     description="API làm mới access token cho admin",
     *     tags={"Admin Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Làm mới token thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="errors", type="null", example=null),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="me",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="mail_address", type="string", example="admin@gmail.com"),
     *                     @OA\Property(property="role", type="integer", example=1)
     *                 ),
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
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function refresh(): JsonResponse
    {
        return $this->sendResponse($this->authService->refreshToken());
    }

    /**
     * me
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/auth/admin/me",
     *     summary="Lấy thông tin admin hiện tại",
     *     description="API lấy thông tin admin đang đăng nhập",
     *     tags={"Admin Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="mail_address", type="string", example="admin@gmail.com"),
     *                 @OA\Property(property="role", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/api/auth/admin/change-password",
     *     summary="Đổi mật khẩu admin",
     *     description="API đổi mật khẩu cho tài khoản admin",
     *     tags={"Admin Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="old_password",
     *                 type="string",
     *                 format="password",
     *                 example="123456Hh@",
     *                 description="Mật khẩu cũ"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="NewPass123@",
     *                 description="Mật khẩu mới"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 example="NewPass123@",
     *                 description="Xác nhận mật khẩu mới"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đổi mật khẩu thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Lỗi validation hoặc mật khẩu cũ không đúng"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
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
     * logout
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/auth/admin/logout",
     *     summary="Đăng xuất admin",
     *     description="API đăng xuất tài khoản admin",
     *     tags={"Admin Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Đăng xuất thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Đăng xuất thành công"),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
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
}
