<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\ApplicationFile\ApplicationFileCollection;
use App\Services\Admin\ApplicationFileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApplicationFileController extends BaseController
{
    protected $applicationFileService;

    /**
     * ApplicationFileController constructor.
     */
    public function __construct(ApplicationFileService $applicationFileService)
    {
        $this->middleware($this->authMiddleware());
        $this->applicationFileService = $applicationFileService;
    }

    /**
     * Method list
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->applicationFileService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new ApplicationFileCollection($data));
    }
}
