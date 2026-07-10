<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Applicant\Favorite\JobFavoriteCollection;
use App\Services\Applicant\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $favoriteService;

    /**
     * FavoriteController constructor.
     */
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function list(Request $request): JsonResponse
    {
        $data = $this->favoriteService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobFavoriteCollection($data));
    }

    public function listJobs(): JsonResponse
    {
        $data = $this->favoriteService->listJobs();
        return $this->sendResponse($data);
    }

    public function toggleJob(string $slug): JsonResponse
    {
        $data = $this->favoriteService->toggleJob($slug);
        return $this->sendResponse($data, 'update');
    }
}
