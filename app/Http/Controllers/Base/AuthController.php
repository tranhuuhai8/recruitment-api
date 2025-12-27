<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\BaseAuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * Method forgotPassword
     *
     * @param ForgotPasswordRequest $request [explicite description]
     *
     * @return void
     *
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Quên mật khẩu",
     *     description="API gửi email reset mật khẩu",
     *     tags={"Authentication"},
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Email không tồn tại"
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = BaseAuthService::getInstance()->forgotPassword($request->get('mail_address'));
        return $this->sendResponse($data);
    }

    /**
     * Method resetPassword
     *
     * @param string $token [explicite description]
     * @param ResetPasswordRequest $request [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/auth/reset-password/{token}",
     *     summary="Đặt lại mật khẩu",
     *     description="API đặt lại mật khẩu bằng token từ email",
     *     tags={"Authentication"},
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
        $data = BaseAuthService::getInstance()->resetPassword($token, $request->only(['mail_address', 'password']));
        return $this->sendResponse($data);
    }
}
