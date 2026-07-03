<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Applicant\FollowedCompanyCollection;
use App\Services\Applicant\CompanyFollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyFollowController extends Controller
{
    protected $companyFollowService;

    /**
     * CompanyFollowController constructor.
     */
    public function __construct(CompanyFollowService $companyFollowService)
    {
        $this->companyFollowService = $companyFollowService;
    }

    public function list(Request $request): JsonResponse
    {
        $data = $this->companyFollowService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new FollowedCompanyCollection($data));
    }

    public function listCompanies(): JsonResponse
    {
        $data = $this->companyFollowService->listCompanies();
        return $this->sendResponse($data);
    }

    public function toggleCompany(string $slug): JsonResponse
    {
        $data = $this->companyFollowService->toggleCompany($slug);
        return $this->sendResponse($data, 'update');
    }

    public function toggleNotify(string $slug): JsonResponse
    {
        $data = $this->companyFollowService->toggleNotify($slug);
        return $this->sendResponse($data, 'update');
    }
}
