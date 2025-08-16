<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Applicant\UpdateApplicantRequest;
use App\Services\Admin\ApplicantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicantController extends BaseController
{
    protected $applicantService;

    /**
     * ApplicantController constructor.
     */
    public function __construct(ApplicantService $applicantService)
    {
        $this->middleware($this->authMiddleware());
        $this->applicantService = $applicantService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->applicantService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse($data);
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
        $data = $this->applicantService->detail($id);
        return $this->sendResponse($data);
    }

    /**
     * Method update
     *
     * @param UpdateApplicantRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateApplicantRequest $request, int $id): JsonResponse
    {
        $data = $this->applicantService->update($request->only($this->getFields()), $id);
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
            'avatar',
            'name',
            'gender',
            'birthday',
            'mail_address',
            'telephone',
            'address',
            'description',
            'status',
        ];
    }
}
