<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\City\CityCollection;
use App\Services\Home\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

// use Illuminate\Support\Facades\Cache;

class CityController extends Controller
{
    protected $cityService;

    /**
     * CityController constructor.
     */
    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/master-data/cities",
     *     summary="Danh sách thành phố",
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
        // $cacheKey = 'home_cities_' . md5(json_encode($params));
        // $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
        //     return $this->cityService::getInstance()->data(...$params);
        // });
        $data = $this->cityService::getInstance()->data(...$params);

        return $this->sendSuccessResponse(new CityCollection($data));
    }

    /**
     * Method listParent
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/master-data/cities-parent",
     *     summary="Danh sách thành phố cha",
     *     tags={"Home - Master Data"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function listParent(Request $request): JsonResponse
    {
        $params = $this->getParamRequest($request);
        // $cacheKey = 'home_cities_parent_' . md5(json_encode($params));
        // $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
        //     return $this->cityService->dataParent($params);
        // });
        $data = $this->cityService->dataParent($params);

        return $this->sendSuccessResponse(new CityCollection($data));
    }
}
