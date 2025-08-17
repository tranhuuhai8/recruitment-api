<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\BaseAuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Method forgotPassword
     *
     * @param ForgotPasswordRequest $request [explicite description]
     *
     * @return void
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
     */
    public function resetPassword(string $token, ResetPasswordRequest $request): JsonResponse
    {
        $data = BaseAuthService::getInstance()->resetPassword($token, $request->only(['mail_address', 'password']));
        return $this->sendResponse($data);
    }
}
