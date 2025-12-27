<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\JobCategory\JobCategoryCollection;
use App\Services\Home\JobCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

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
     *
     * @OA\Get(
     *     path="/api/home/master-data/job-categories",
     *     summary="Danh sách danh mục công việc",
     *     tags={"Home - Master Data"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="orders", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $params = $this->getParamRequest($request);
        // $cacheKey = 'home_categories_' . md5(json_encode($params));
        // $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
        //     return $this->jobCategoryService::getInstance()->data(...$params);
        // });
        $data = $this->jobCategoryService::getInstance()->data(...$params);
        return $this->sendSuccessResponse(new JobCategoryCollection($data));
    }

    /**
     * Method listParent
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/master-data/job-categories-parent",
     *     summary="Danh sách danh mục công việc cha",
     *     tags={"Home - Master Data"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function listParent(Request $request): JsonResponse
    {
        $params = $this->getParamRequest($request);
        // $cacheKey = 'home_categories_parent_' . md5(json_encode($params));
        // $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
        //     return $this->jobCategoryService->dataParent($params);
        // });
        $data = $this->jobCategoryService->dataParent($params);
        return $this->sendSuccessResponse(new JobCategoryCollection($data));
    }
}
