<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests\Company\Apply\UpdateJobApplyRequest;
use App\Http\Resources\Company\Apply\JobApplyCollection;
use App\Services\Company\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends BaseController
{
    protected $jobApplicationService;

    /**
     * JobApplicationController constructor.
     */
    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->middleware($this->authMiddleware());
        $this->jobApplicationService = $jobApplicationService;
    }

    /**
     * Method list
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobApplicationService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobApplyCollection($data));
    }

    /**
     * Method update
     *
     * @param UpdateJobApplyRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateJobApplyRequest $request, int $id): JsonResponse
    {
        $data = $this->jobApplicationService->update($request->only('status'), $id);
        return $this->sendResponse($data, 'update');
    }

    /**
     * Method delete
     *
     * @param int $id [explicite description]
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $data = $this->jobApplicationService->delete($id);
        return $this->sendResponse($data, 'delete');
    }
}
