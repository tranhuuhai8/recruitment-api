<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\Company\CompanyCollection;
use App\Services\Home\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyService;

    /**
     * CompanyController constructor.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->companyService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new CompanyCollection($data));
    }
}
