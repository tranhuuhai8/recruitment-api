<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\Job\ApplyJobRequest;
use App\Http\Resources\Home\Job\JobCollection;
use App\Services\Home\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $jobService;

    /**
     * JobController constructor.
     */
    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobCollection($data));
    }

    /**
     * Method detail
     *
     * @param int $id [explicite description]
     *
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        $data = $this->jobService->detail($id);
        return $this->sendResponse($data);
    }

    /**
     * Method apply
     *
     * @param ApplyJobRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function apply(ApplyJobRequest $request): JsonResponse
    {
        $data = $this->jobService->apply($request->only($this->getFields()));
        return $this->sendResponse($data, '', trans('response.job.apply_success'));
    }

    /**
     * Method getCv
     *
     * @return JsonResponse
     */
    public function getCv(): JsonResponse
    {
        $data = $this->jobService->getCv();
        return $this->sendResponse($data);
    }

    /**
     * getFields
     *
     * @return array
     */
    public function getFields(): array
    {
        return [
            'applicant_id',
            'job_id',
            'file_name',
            'file_path',
            'file_size',
            'application_file_id',
            'cover_letter',
            'guest_name',
            'guest_email',
            'guest_telephone',
            'source_cv',
        ];
    }
}
