<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $guard = 'api';

    /**
     * constructor
     *
     */
    public function __construct()
    {
        // $this->middleware('logRequest');
    }


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
     * @param string $message
     * @param mixed $errors
     * @param integer $code
     * @return JsonResponse
     */
    protected function sendErrorResponse($message, $errors = null, $code = ResponseHelper::STATUS_CODE_BAD_REQUEST): JsonResponse
    {
        return ResponseHelper::sendResponse($code, $message, null, $errors);
    }

    /**
     * Send Success Response
     *
     * @param $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function sendSuccessResponse($data, $message = '', $code = ResponseHelper::STATUS_CODE_SUCCESS): JsonResponse
    {
        return ResponseHelper::sendResponse($code, $message, $data);
    }

    /**
     * sendResponse
     *
     * @param $data
     * @param string $type
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
}
