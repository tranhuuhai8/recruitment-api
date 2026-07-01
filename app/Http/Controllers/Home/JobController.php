<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\Job\ApplyJobRequest;
use App\Http\Resources\Home\Job\JobCollection;
use App\Services\Home\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

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
     *
     * @OA\Get(
     *     path="/api/home/job",
     *     summary="Danh sách công việc",
     *     tags={"Home - Job"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="orders", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thành công")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->jobService::getInstance()->data(...$this->getParamRequest($request));
        return $this->sendSuccessResponse(new JobCollection($data));
    }

    /**
     * Method detail
     *
     * @param string $slug [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/home/job/{slug}",
     *     summary="Chi tiết công việc",
     *     tags={"Home - Job"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function detail(string $slug): JsonResponse
    {
        $data = $this->jobService->detail($slug);
        return $this->sendResponse($data);
    }

    /**
     * Method apply
     *
     * @param ApplyJobRequest $request [explicite description]
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/home/job/apply",
     *     summary="Ứng tuyển công việc",
     *     tags={"Home - Job"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id", "cover_letter"},
     *             @OA\Property(property="job_id", type="integer", example=1),
     *             @OA\Property(property="applicant_id", type="integer", example=1, nullable=true),
     *             @OA\Property(property="application_file_id", type="integer", nullable=true),
     *             @OA\Property(property="file_name", type="string", nullable=true),
     *             @OA\Property(property="file_path", type="string", nullable=true),
     *             @OA\Property(property="file_size", type="integer", nullable=true),
     *             @OA\Property(property="cover_letter", type="string", example="Tôi muốn ứng tuyển..."),
     *             @OA\Property(property="guest_name", type="string", nullable=true),
     *             @OA\Property(property="guest_email", type="string", format="email", nullable=true),
     *             @OA\Property(property="guest_telephone", type="string", nullable=true),
     *             @OA\Property(property="source_cv", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ứng tuyển thành công"),
     *     @OA\Response(response=422, description="Lỗi validation")
     * )
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
     *
     * @OA\Get(
     *     path="/api/home/job/get-cv",
     *     summary="Lấy danh sách CV của applicant",
     *     tags={"Home - Job"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Thành công"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
