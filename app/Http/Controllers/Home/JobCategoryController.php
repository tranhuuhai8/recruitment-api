<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\JobCategory\JobCategoryCollection;
use App\Services\Home\JobCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobCategoryController extends Controller
{
    protected $jobCategoryService;

    /**
     * JobCategoryController constructor.
     */
    public function __construct(JobCategoryService $jobCategoryService)
    {
        $this->jobCategoryService = $jobCategoryService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobCategoryService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobCategoryCollection($data));
    }

    /**
     * Method listParent
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function listParent(Request $request): JsonResponse
    {
        $data = $this->jobCategoryService->dataParent($this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobCategoryCollection($data));
    }
}
