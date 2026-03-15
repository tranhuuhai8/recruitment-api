<?php

namespace App\Http\Controllers\Company;

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

        $this->authService = $authService;
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
