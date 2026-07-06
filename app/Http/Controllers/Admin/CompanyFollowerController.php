<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CompanyFollower\CompanyFollowerCollection;
use App\Services\Admin\CompanyFollowerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyFollowerController extends Controller
{
    protected $companyFollowerService;

    /**
     * CompanyFollowerController constructor.
     */
    public function __construct(CompanyFollowerService $companyFollowerService)
    {
        $this->companyFollowerService = $companyFollowerService;
    }

    /**
     * Method list
     *
     * Returns individual follower list (applicants) for a single company,
     * unlike list() which returns a cross-company aggregate.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->companyFollowerService->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new CompanyFollowerCollection($data));
    }
}
