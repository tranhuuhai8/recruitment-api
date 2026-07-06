<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\Favorite\JobFavoriteCollection;
use App\Services\Admin\JobFavoriteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobFavoriteController extends BaseController
{
    protected $jobFavoriteService;

    /**
     * JobFavoriteController constructor.
     */
    public function __construct(JobFavoriteService $jobFavoriteService)
    {
        $this->middleware($this->authMiddleware());
        $this->jobFavoriteService = $jobFavoriteService;
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
        $data = $this->jobFavoriteService->data(...$this->getParamRequest($request));
        return $this->sendResponse(new JobFavoriteCollection($data));
    }
}
