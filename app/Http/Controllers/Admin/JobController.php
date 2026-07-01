<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Job\UpdateJobRequest;
use App\Http\Resources\Admin\Job\JobCollection;
use App\Services\Admin\JobService;
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
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function detail(string $slug): JsonResponse
    {
        $data = $this->jobService->detail($slug);
        return $this->sendResponse($data);
    }

    /**
     * Method update
     *
     * @param UpdateJobRequest $request
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function update(UpdateJobRequest $request, string $slug): JsonResponse
    {
        $data = $this->jobService->update($request->only($this->getFields()), $slug);
        return $this->sendResponse($data, 'update');
    }

    /**
     * Method delete
     *
     * @param string $slug [explicite description]
     *
     * @return JsonResponse
     */
    public function delete(string $slug): JsonResponse
    {
        $data = $this->jobService->delete($slug);
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
            'salary_min',
            'salary_max',
            'start_date',
            'end_date',
            'city_id',
            'job_category_id',
            'status',
            'type',
        ];
    }
}
