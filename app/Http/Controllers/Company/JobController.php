<?php

namespace App\Http\Controllers\Company;

use App\Http\Resources\Company\Job\JobCollection;
use App\Services\Company\JobService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobController extends BaseController
{
    protected $jobService;

    /**
     * JobController constructor.
     */
    public function __construct(JobService $jobService)
    {
        $this->middleware($this->authMiddleware());
        $this->jobService = $jobService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobCollection($data));
    }
}
