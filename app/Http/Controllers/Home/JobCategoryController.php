<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\JobCategory\JobCategoryCollection;
use App\Services\Home\JobCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $params = $this->getParamRequest($request);
        $cacheKey = 'home_categories_' . md5(json_encode($params));
        $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
            return $this->jobCategoryService::getInstance()->data(...$params);
        });
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
        $params = $this->getParamRequest($request);
        $cacheKey = 'home_categories_parent_' . md5(json_encode($params));
        $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
            return $this->jobCategoryService->dataParent($params);
        });
        return $this->sendSuccessResponse(new JobCategoryCollection($data));
    }
}
