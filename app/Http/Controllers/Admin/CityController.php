<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\City\CreateCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;
use App\Http\Resources\Admin\MasterData\CitiesCollection;
use App\Services\Admin\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Cache;

class CityController extends BaseController
{
    protected $cityService;

    /**
     * CityController constructor.
     */
    public function __construct(CityService $cityService)
    {
        $this->middleware($this->authMiddleware());
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
        // $cacheKey = 'admin_cities' . md5(json_encode($params));
        // $data = Cache::remember($cacheKey, config('cache.ttl'), function () use ($params) {
        //     return $this->cityService::getInstance()->data(...$params);
        // });
        $data = $this->cityService::getInstance()->data(...$params);

        return $this->sendSuccessResponse(new CitiesCollection($data));
    }

    /**
     * store
     *
     * @param  CreateCityRequest $request
     * @return JsonResponse
     */
    public function store(CreateCityRequest $request): JsonResponse
    {
        $data = $this->cityService->store($request->only($this->getFieldKeys()));
        return $this->sendResponse($data, 'create');
    }

    /**
     * update
     *
     * @param  UpdateCityRequest $request
     * @return JsonResponse
     */
    public function update(UpdateCityRequest $request, int $id): JsonResponse
    {
        $data = $this->cityService->update($request->only($this->getFieldKeys()), $id);
        return $this->sendResponse($data, 'update');
    }

    /**
     * delete
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $data = $this->cityService->delete($id);
        return $this->sendResponse($data, 'delete');
    }

    /**
     * getFieldKeys
     *
     * @return array
     */
    public function getFieldKeys(): array
    {
        return [
            'name',
            'description',
            'status',
            'parent_id',
        ];
    }
}
