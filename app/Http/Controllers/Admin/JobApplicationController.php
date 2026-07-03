<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\Apply\JobApplyCollection;
use App\Services\Admin\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends BaseController
{
    protected $jobApplicationService;

    /**
     * JobApplicationController constructor.
     */
    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->middleware($this->authMiddleware());
        $this->jobApplicationService = $jobApplicationService;
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
        $data = $this->jobApplicationService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobApplyCollection($data));
    }
}
