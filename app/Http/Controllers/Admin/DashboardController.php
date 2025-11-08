<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    protected $dashboardService;

    /**
     * DashboardController constructor.
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware($this->authMiddleware());
        $this->dashboardService = $dashboardService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->dashboardService->list();
        return $this->sendSuccessResponse($data);
    }
}
