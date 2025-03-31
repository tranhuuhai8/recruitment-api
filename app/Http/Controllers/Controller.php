<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $guard = 'api';

    /**
     * Get the guest middleware for the application.
     *
     * @return string
     */
    public function guestMiddleware()
    {
        $guard = $this->getGuard();
        return $guard ? ('guest:' . $guard) : 'guest';
    }

    /**
     * Get the auth middleware for the application.
     *
     * @return string
     */
    public function authMiddleware()
    {
        $guard = $this->getGuard();
        return $guard ? ('auth:' . $guard) : 'auth';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return string
     */
    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : config('auth.defaults.guard');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard($this->getGuard());
    }

    /**
     * Send Error Response
     *
     * @param  string  $message
     * @param  mixed   $errors
     * @param  integer $code
     * @return JsonResponse
     */
    protected function sendErrorResponse(
        string $message,
        $errors = null,
        int $code = ResponseHelper::STATUS_CODE_BAD_REQUEST
    ): JsonResponse {
        return ResponseHelper::sendResponse($code, $message, null, $errors);
    }

    /**
     * Send Success Response
     *
     * @param  mixed $data
     * @param  string $message
     * @param  int    $code
     * @return JsonResponse
     */
    protected function sendSuccessResponse(
        $data,
        string $message = '',
        int $code = ResponseHelper::STATUS_CODE_SUCCESS
    ): JsonResponse {
        return ResponseHelper::sendResponse($code, $message, $data);
    }

    /**
     * sendResponse
     *
     * @param  $data
     * @param  string $type
     * @return JsonResponse
     */
    protected function sendResponse($data, string $type = 'list', string $message = null, $code = null): JsonResponse
    {
        if (data_get($data, 'message')) {
            return $this->sendErrorResponse($data['message'], '', data_get($data, 'status_code'));
        }
        return $this->sendSuccessResponse(
            $data,
            $message ?? trans('response.' . $type . '_success'),
            $code ?? ResponseHelper::STATUS_CODE_SUCCESS
        );
    }

    /**
     * Method getParamRequest
     *
     * @param Request $request
     *
     * @return array
     */
    public function getParamRequest(Request $request): array
    {
        $search = data_get($request, 'search');
        $perPage = data_get($request, 'per_page');
        $orders = data_get($request, 'orders');
        $filters = data_get($request, 'filters');
        $all = data_get($request, 'all');

        return [$search, $orders, $filters, $perPage, $all];
    }
}
