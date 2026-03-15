<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\UnifiedAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use App\Services\Auth\BaseAuthService;

class UnifiedAuthController extends Controller
{
    public function __construct(
        private readonly UnifiedAuthService $authService
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="unifiedLogin",
     *     tags={"Auth"},
     *     summary="Đăng nhập (dùng chung cho tất cả role)",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address","password"},
     *             @OA\Property(property="mail_address", type="string", format="email",
     *                          example="admin@gmail.com"),
     *             @OA\Property(property="password", type="string",
     *                          minLength=6, example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string",
     *                          example="Đăng nhập thành công."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="token_type", type="string",
     *                              example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer",
     *                              example=3600),
     *                 @OA\Property(property="role", type="string",
     *                              enum={"admin","company","applicant"}),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Sai email hoặc mật khẩu"),
     *     @OA\Response(response=422, description="Validation thất bại")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if ($request->get('token')) {
                BaseAuthService::getInstance()->verifyAccount($request->get('token'));
            }
            $result = $this->authService->login($request->validated());
            return $this->sendResponse($result, '', 'Đăng nhập thành công.');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), null, 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     operationId="unifiedRefresh",
     *     summary="Làm mới token",
     *     description="API làm mới access token",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Làm mới token thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();
            return $this->sendResponse($result, '', 'Làm mới token thành công.');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), null, 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     operationId="unifiedMe",
     *     summary="Lấy thông tin người dùng hiện tại",
     *     description="API lấy thông tin người dùng đang đăng nhập",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(): JsonResponse
    {
        return $this->sendSuccessResponse($this->authService->me());
    }

    /**
     * @OA\Post(
     *     path="/api/auth/change-password",
     *     operationId="unifiedChangePassword",
     *     summary="Đổi mật khẩu",
     *     description="API đổi mật khẩu cho tài khoản",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "password", "password_confirmation"},
     *             @OA\Property(property="old_password", type="string", format="password", minLength=6),
     *             @OA\Property(property="password", type="string", format="password", minLength=6),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=6)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Đổi mật khẩu thành công"),
     *     @OA\Response(response=400, description="Lỗi validation hoặc mật khẩu cũ không đúng"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function changePassword(\App\Http\Requests\Auth\ChangePasswordRequest $request): JsonResponse
    {
        $data = $this->authService->changePassword($request->only(['old_password', 'password']));
        return $this->sendResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="unifiedLogout",
     *     summary="Đăng xuất",
     *     description="API đăng xuất",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Đăng xuất thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return $this->sendSuccessResponse(true, trans('auth.logout_success'));
    }
}
