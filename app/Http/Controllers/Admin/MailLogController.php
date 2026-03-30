<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\MailLog\MailLogCollection;
use App\Http\Resources\Admin\MailLog\MailLogResource;
use App\Services\Admin\MailLogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailLogController extends BaseController
{
    public function __construct(
        private readonly MailLogService $mailLogService
    ) {
        $this->middleware($this->authMiddleware());
    }

    /**
     * @OA\Get(
     *      path="/api/admin/mail-log",
     *      operationId="adminGetMailLogList",
     *      tags={"Admin - Mail Log"},
     *      summary="Lấy danh sách lịch sử gửi email",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Tìm kiếm theo email, tên người nhận, tiêu đề",
     *          required=false,
     *          @OA\Schema(type="string", example="user@example.com")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer", example=15)
     *      ),
     *      @OA\Parameter(
     *          name="orders[0][key]",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string", example="created_at")
     *      ),
     *      @OA\Parameter(
     *          name="orders[0][dir]",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string", enum={"ascending", "descending"})
     *      ),
     *      @OA\Parameter(
     *          name="filters[0][key]",
     *          in="query",
     *          description="Khóa lọc: status | contact_id",
     *          required=false,
     *          @OA\Schema(type="string", example="status")
     *      ),
     *      @OA\Parameter(
     *          name="filters[0][data]",
     *          in="query",
     *          description="Giá trị lọc (status: 1=Pending,2=Sent,3=Failed,4=Bounced)",
     *          required=false,
     *          @OA\Schema(type="string", example="3")
     *      ),
     *
     *      @OA\Response(response=200, description="Thành công"),
     *      @OA\Response(response=401, description="Chưa xác thực")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->mailLogService::getInstance()->data(...$this->getParamRequest($request));

        return $this->sendSuccessResponse(new MailLogCollection($data));
    }

    /**
     * @OA\Get(
     *      path="/api/admin/mail-log/{id}",
     *      operationId="adminGetMailLogDetail",
     *      tags={"Admin - Mail Log"},
     *      summary="Xem chi tiết mail log (bao gồm nội dung email đã gửi)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\Response(response=200, description="Thành công"),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function detail(int $id): JsonResponse
    {
        $data = $this->mailLogService->detail($id);
        if (is_array($data)) {
            return $this->sendResponse($data);
        }

        return $this->sendSuccessResponse(new MailLogResource($data));
    }

    /**
     * @OA\Post(
     *      path="/api/admin/mail-log/{id}/retry",
     *      operationId="adminRetryMailLog",
     *      tags={"Admin - Mail Log"},
     *      summary="Retry gửi lại email thất bại",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID của mail log có status=Failed",
     *          @OA\Schema(type="integer", example=5)
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Retry thành công, email đã được đưa vào queue",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Email đã được thêm vào hàng chờ gửi lại.")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Chỉ retry được email có trạng thái Failed"),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function retry(int $id): JsonResponse
    {
        try {
            $data = $this->mailLogService->retry($id);

            return $this->sendResponse($data);
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }
}
