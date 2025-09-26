<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public const STATUS_CODE_SUCCESS = 200;
    public const STATUS_CODE_BAD_REQUEST = 400;
    public const STATUS_CODE_UNAUTHORIZED = 401;
    public const STATUS_CODE_FORBIDDEN = 403;
    public const STATUS_CODE_NOT_FOUND = 404;
    public const STATUS_CODE_VALIDATE_ERROR = 422;
    public const STATUS_CODE_SERVER_ERROR = 500;
    public const HTTP_TOO_MANY_REQUESTS = 429;

    /**
     * Method sendResponse
     *
     * @param $code
     * @param $message
     * @param $data
     * @param $errors
     *
     * @return JsonResponse
     */
    public static function sendResponse($code, $message, $data = null, $errors = null): JsonResponse
    {
        return response()->json([
            'status_code' => $code,
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
        ]);
    }

    /**
     * Method notFound
     *
     * @return array
     */
    public static function notFound($message = ''): array
    {
        return [
            'message' => $message ?: trans("response.data_not_found"),
            'status_code' => ResponseHelper::STATUS_CODE_NOT_FOUND,
        ];
    }

    /**
     * sendError
     *
     * @param  string $message
     * @param  $code
     * @return array
     */
    public static function sendError(string $message, $code = self::STATUS_CODE_BAD_REQUEST): array
    {
        return [
            'message' => $message,
            'status_code' => $code,
        ];
    }
}
