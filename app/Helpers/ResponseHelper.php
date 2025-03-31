<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    const STATUS_CODE_SUCCESS = 200;
    const STATUS_CODE_BAD_REQUEST = 400;
    const STATUS_CODE_UNAUTHORIZED = 401;
    const STATUS_CODE_FORBIDDEN = 403;
    const STATUS_CODE_NOT_FOUND = 404;
    const STATUS_CODE_VALIDATE_ERROR = 422;
    const STATUS_CODE_SERVER_ERROR = 500;
    const HTTP_TOO_MANY_REQUESTS = 429;
    
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
     * @param string $text
     *
     * @return array
     */
    public static function notFound(string $text): array
    {
        return [
            'message' => 'Không tìm thấy '. $text,
            'status_code' => ResponseHelper::STATUS_CODE_NOT_FOUND,
        ];
    }
}
