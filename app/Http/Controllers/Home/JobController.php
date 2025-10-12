<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\Job\JobCollection;
use App\Services\Home\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $jobService;

    /**
     * JobController constructor.
     */
    public function __construct(JobService $jobService)
    {
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
        $data = $this->jobService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobCollection($data));
    }
}
