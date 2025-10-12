<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\City\CityCollection;
use App\Services\Home\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
