<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Requests\Applicant\Information\UpdateInfoRequest;
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

        $this->authService = $authService;
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
