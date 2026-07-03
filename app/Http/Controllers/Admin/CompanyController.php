<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Company\UpdateCompanyRequest;
use App\Http\Resources\Admin\Company\CompanyResource;
use App\Services\Admin\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    protected $companyService;

    /**
     * CompanyController constructor.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->middleware($this->authMiddleware());
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
        $data = $this->companyService->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse($data);
    }

    /**
     * listCompany
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function listCompany(Request $request): JsonResponse
    {
        $data = $this->companyService->listCompany($this->getParamRequest($request));
        return $this->sendResponse($data);
    }

    /**
     * Method detail
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        $data = $this->companyService->detail($id);
        return $this->sendResponse(new CompanyResource($data));
    }

    /**
     * Method update
     *
     * @param UpdateCompanyRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        $data = $this->companyService->update($request->only($this->getFields()), $id);
        return $this->sendResponse($data, 'update');
    }

    /**
     * getFields
     *
     * @return array
     */
    public function getFields(): array
    {
        return [
            'logo',
            'cover_img',
            'name',
            'short_name',
            'mail_address',
            'telephone',
            'city_id',
            'address',
            'website',
            'status',
            'description',
        ];
    }
}
