<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Resources\Applicant\Apply\JobApplyCollection;
use App\Services\Applicant\ApplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplyController extends BaseController
{
    protected $applyService;

    /**
     * ApplyController constructor.
     */
    public function __construct(ApplyService $applyService)
    {
        $this->middleware($this->authMiddleware());
        $this->applyService = $applyService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->applyService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobApplyCollection($data));
    }
}
