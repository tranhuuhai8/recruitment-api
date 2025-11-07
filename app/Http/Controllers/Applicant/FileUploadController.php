<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Requests\Applicant\File\UpsertApplicationFileRequest;
use App\Services\Applicant\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileUploadController extends BaseController
{
    protected $fileUploadService;

    /**
     * JobController constructor.
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->middleware($this->authMiddleware());
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $data = $this->fileUploadService->list();
        return $this->sendResponse($data);
    }
    /**
     * Method upsert
     *
     * @param UpsertApplicationFileRequest $request
     *
     * @return JsonResponse
     */
    public function upsert(UpsertApplicationFileRequest $request): JsonResponse
    {
        $data = $this->fileUploadService->upsert($request->all());
        return $this->sendResponse($data, 'update');
    }
}
