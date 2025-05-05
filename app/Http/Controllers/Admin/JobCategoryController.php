<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\JobCategory\CreateJobCategoryRequest;
use App\Http\Requests\Admin\JobCategory\UpdateJobCategoryRequest;
use App\Services\Admin\JobCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobCategoryController extends BaseController
{
    protected $jobCategoryService;

    /**
     * JobCategoryController constructor.
     */
    public function __construct(JobCategoryService $jobCategoryService)
    {
        $this->middleware($this->authMiddleware());
        $this->jobCategoryService = $jobCategoryService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobCategoryService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse($data);
    }

    /**
     * store
     *
     * @param  CreateJobCategoryRequest $request
     * @return JsonResponse
     */
    public function store(CreateJobCategoryRequest $request): JsonResponse
    {
        $data = $this->jobCategoryService->store($request->only($this->getFieldKeys()));
        return $this->sendResponse($data, 'create');
    }

    /**
     * update
     *
     * @param  UpdateJobCategoryRequest $request
     * @return JsonResponse
     */
    public function update(UpdateJobCategoryRequest $request, int $id): JsonResponse
    {
        $data = $this->jobCategoryService->update($request->only($this->getFieldKeys()), $id);
        return $this->sendResponse($data, 'update');
    }

    /**
     * delete
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $data = $this->jobCategoryService->delete($id);
        return $this->sendResponse($data, 'delete');
    }

    /**
     * getFieldKeys
     *
     * @return array
     */
    public function getFieldKeys(): array
    {
        return [
            'name',
            'description',
            'status',
            'type',
            'parent_id',
        ];
    }
}
