<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests\Company\Job\CreateJobRequest;
use App\Http\Requests\Company\Job\UpdateJobRequest;
use App\Http\Resources\Company\Job\JobCollection;
use App\Http\Resources\Company\Job\JobDetailResource;
use App\Services\Company\JobService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobController extends BaseController
{
    protected $jobService;

    /**
     * JobController constructor.
     */
    public function __construct(JobService $jobService)
    {
        $this->middleware($this->authMiddleware());
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
        $data = $this->jobService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobCollection($data));
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
        $data = $this->jobService->detail($id);
        return $this->sendResponse($data);
    }

    /**
     * Method update
     *
     * @param UpdateJobRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateJobRequest $request, int $id): JsonResponse
    {
        $data = $this->jobService->update($request->only($this->getFields()), $id);
        return $this->sendResponse($data, 'update');
    }

    /**
     * Method store
     *
     * @param CreateJobRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        $data = $this->jobService->store($request->only([...$this->getFields(), 'company_id']));
        return $this->sendResponse($data, 'create');
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
        $data = $this->jobService->delete($id);
        return $this->sendResponse($data, 'delete');
    }

    /**
     * getFields
     *
     * @return array
     */
    public function getFields(): array
    {
        return [
            'title',
            'banner',
            'description',
            'number_of_recruitment',
            'mail_address',
            'salary_min',
            'salary_max',
            'start_date',
            'end_date',
            'city_id',
            'job_category_id',
            'status',
            'type',
            'notify_frequency',
        ];
    }
}
