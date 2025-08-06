<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\City\CityCollection;
use App\Services\Home\CityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $data = $this->cityService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new CityCollection($data));
    }
}
