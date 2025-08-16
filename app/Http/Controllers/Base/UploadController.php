<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Services\Base\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $uploadService;

    /**
     * UploadController constructor.
     */
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Method uploadImg
     *
     * @param Request $request [explicite description]
     *
     * @return void
     */
    public function uploadImg(Request $request): JsonResponse
    {
        $data = $this->uploadService->uploadImg($request);
        return $this->sendResponse($data);
    }
}
