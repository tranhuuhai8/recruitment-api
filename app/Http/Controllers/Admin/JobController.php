<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\Job\JobCollection;
use App\Services\Admin\JobService;
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

    /**
     * Method delete
     *
     * @param int $id [explicite description]
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $data = $this->jobService->delete($id);
        return $this->sendResponse($data, 'delete');
    }
}
