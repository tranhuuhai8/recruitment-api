<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\Company\CompanyCollection;
use App\Services\Home\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CompanyController extends Controller
{
    protected $companyService;

    /**
     * CompanyController constructor.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * list
     *
     * @param  Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/company",
     *     summary="Danh sách công ty",
     *     tags={"Home - Company"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="orders", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->companyService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new CompanyCollection($data));
    }

    /**
     * Method detail
     *
     * @param string $slug [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/company/{slug}",
     *     summary="Chi tiết công ty",
     *     tags={"Home - Company"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function detail(string $slug): JsonResponse
    {
        $data = $this->companyService->detail($slug);
        return $this->sendResponse($data);
    }
}
