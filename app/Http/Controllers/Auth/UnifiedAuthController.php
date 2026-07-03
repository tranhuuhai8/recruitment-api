<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Auth\BaseAuthService;
use App\Helpers\ResponseHelper;

class UnifiedAuthController extends Controller
{
    public function __construct(
        private readonly BaseAuthService $authService
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
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string",
     *                              example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer",
     *                              example=3600),
     *                 @OA\Property(property="role", type="string",
     *                              enum={"admin","company","applicant"}),
     *                 @OA\Property(property="me", type="object")
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
            $result = $this->authService->login(auth('api'), $request->validated());
            $cookie = $result['refresh_cookie'] ?? null;
            unset($result['refresh_cookie']);

            $response = $this->sendResponse($result, '', 'Đăng nhập thành công.');
            return $cookie ? $response->withCookie($cookie) : $response;
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), null, ResponseHelper::STATUS_CODE_UNAUTHORIZED);
        }
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        try {
            $this->authService->verifyAccount((int) $request->query('id'));
            return redirect(config('app.url_home') . '/tai-khoan/xac-thuc-email?status=success');
        } catch (Exception) {
            return redirect(config('app.url_home') . '/tai-khoan/xac-thuc-email?status=expired');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     operationId="unifiedRefresh",
     *     summary="Làm mới token",
     *     description="Làm mới access token từ refresh_token cookie (HttpOnly). Không cần Authorization header.",
     *     tags={"Auth"},
     *     @OA\Response(response=200, description=""),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->cookie('refresh_token'), auth('api'));
            $cookie = $result['refresh_cookie'] ?? null;
            unset($result['refresh_cookie']);

            $response = $this->sendResponse($result);
            return $cookie ? $response->withCookie($cookie) : $response;
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), null, ResponseHelper::STATUS_CODE_UNAUTHORIZED);
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
        return $this->sendSuccessResponse($this->authService->me(auth('api')));
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
        $data = $this->authService->changePassword(auth('api'), $request->only(['old_password', 'password']));
        return $this->sendResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="unifiedLogout",
     *     summary="Đăng xuất",
     *     description="API đăng xuất — blacklists access token và xoá refresh_token cookie.",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Đăng xuất thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $clearCookie = $this->authService->logout($request->cookie('refresh_token'), auth('api'));
        return $this->sendSuccessResponse(true, trans('auth.logout_success'))->withCookie($clearCookie);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Quên mật khẩu",
     *     description="API gửi email reset mật khẩu",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address"},
     *             @OA\Property(
     *                 property="mail_address",
     *                 type="string",
     *                 format="email",
     *                 example="user@gmail.com",
     *                 description="Email đăng ký"
     *            )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gửi email thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgotPassword($request->get('mail_address'));
        return $this->sendResponse($data);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password/{token}",
     *     summary="Đặt lại mật khẩu",
     *     description="API đặt lại mật khẩu bằng token từ email",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token reset password từ email",
     *         @OA\Schema(type="string", example="abc123xyz")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mail_address", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="mail_address",
     *                 type="string",
     *                 format="email",
     *                 example="user@gmail.com",
     *                 description="Email đăng ký"
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
     *         description="Đặt lại mật khẩu thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Token không hợp lệ hoặc đã hết hạn"
     *     )
     * )
     */
    public function resetPassword(string $token, ResetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->resetPassword($token, $request->only(['mail_address', 'password']));
        return $this->sendResponse($data);
    }
}
