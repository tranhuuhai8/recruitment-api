<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Contact\CreateContactRequest;
use App\Http\Requests\Admin\Contact\ReplyContactRequest;
use App\Http\Requests\Admin\Contact\UpdateContactRequest;
use App\Http\Resources\Admin\Contact\ContactCollection;
use App\Http\Resources\Admin\Contact\ContactDetailResource;
use App\Services\Admin\ContactService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends BaseController
{
    public function __construct(
        private readonly ContactService $contactService
    ) {
        $this->middleware($this->authMiddleware())->except('store');
    }

    /**
     * @OA\Get(
     *      path="/api/admin/contact",
     *      operationId="adminGetContactList",
     *      tags={"Admin - Contact"},
     *      summary="Lấy danh sách tin nhắn liên hệ",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Tìm kiếm theo tên, email, tiêu đề, nội dung",
     *          required=false,
     *          @OA\Schema(type="string", example="Nguyễn Văn A")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Số bản ghi mỗi trang (mặc định 15)",
     *          required=false,
     *          @OA\Schema(type="integer", example=15)
     *      ),
     *      @OA\Parameter(
     *          name="orders[0][key]",
     *          in="query",
     *          description="Cột sắp xếp (id, created_at, priority, status)",
     *          required=false,
     *          @OA\Schema(type="string", example="created_at")
     *      ),
     *      @OA\Parameter(
     *          name="orders[0][dir]",
     *          in="query",
     *          description="Chiều sắp xếp: ascending | descending",
     *          required=false,
     *          @OA\Schema(type="string", enum={"ascending", "descending"})
     *      ),
     *      @OA\Parameter(
     *          name="filters[0][key]",
     *          in="query",
     *          description="Khóa lọc: status | priority",
     *          required=false,
     *          @OA\Schema(type="string", example="status")
     *      ),
     *      @OA\Parameter(
     *          name="filters[0][data]",
     *          in="query",
     *          description="Giá trị lọc (status | priority)",
     *          required=false,
     *          @OA\Schema(type="string", example="1")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="total", type="integer", example=50),
     *                  @OA\Property(property="per_page", type="integer", example=15),
     *                  @OA\Property(property="current_page", type="integer", example=1)
     *              )
     *          )
     *      ),
     *      @OA\Response(response=401, description="Chưa xác thực"),
     *      @OA\Response(response=403, description="Không có quyền truy cập")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->contactService::getInstance()->data(...$this->getParamRequest($request));

        return $this->sendSuccessResponse(new ContactCollection($data));
    }

    /**
     * @OA\Post(
     *      path="/api/admin/contact",
     *      operationId="adminCreateContact",
     *      tags={"Admin - Contact"},
     *      summary="Tạo tin nhắn liên hệ mới (public - không cần auth)",
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"full_name", "email", "title", "content"},
     *              @OA\Property(property="full_name", type="string", example="Nguyễn Văn A"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="phone", type="string", example="0901234567"),
     *              @OA\Property(property="title", type="string", example="Tôi cần hỗ trợ về tài khoản"),
     *              @OA\Property(property="content", type="string", example="Tôi không thể đăng nhập vào hệ thống...")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Gửi tin nhắn thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Gửi tin nhắn thành công."),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation thất bại"),
     *      @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function store(CreateContactRequest $request): JsonResponse
    {
        try {
            $data = $this->contactService->store($request->validated());

            return $this->sendResponse($data, 'create');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/admin/contact/{id}",
     *      operationId="adminGetContactDetail",
     *      tags={"Admin - Contact"},
     *      summary="Xem chi tiết tin nhắn liên hệ (tự động đánh dấu đã đọc)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID của contact",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=401, description="Chưa xác thực"),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function detail(int $id): JsonResponse
    {
        $data = $this->contactService->detail($id);
        if (is_array($data)) {
            return $this->sendResponse($data);
        }

        return $this->sendSuccessResponse(new ContactDetailResource($data));
    }

    /**
     * @OA\Put(
     *      path="/api/admin/contact/{id}",
     *      operationId="adminUpdateContact",
     *      tags={"Admin - Contact"},
     *      summary="Cập nhật trạng thái, ưu tiên, ghi chú của tin nhắn",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="integer"),
     *              @OA\Property(property="priority", type="integer"),
     *              @OA\Property(property="admin_note", type="string")
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Cập nhật thành công"),
     *      @OA\Response(response=404, description="Không tìm thấy"),
     *      @OA\Response(response=422, description="Validation thất bại")
     * )
     */
    public function update(UpdateContactRequest $request, int $id): JsonResponse
    {
        try {
            $data = $this->contactService->update($request->validated(), $id);

            return $this->sendResponse($data, 'update');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/api/admin/contact/{id}/reply",
     *      operationId="adminReplyContact",
     *      tags={"Admin - Contact"},
     *      summary="Phản hồi email cho user (chọn template + tùy chỉnh nội dung)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID của contact",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"mail_template_id"},
     *              @OA\Property(property="mail_template_id", type="integer", example=2),
     *              @OA\Property(property="subject", type="string", example="Phản hồi yêu cầu của bạn"),
     *              @OA\Property(property="body", type="string", example="<p>Chào bạn, ...</p>")
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Đã gửi phản hồi vào queue",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Phản hồi email đã được gửi thành công."),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Lỗi nghiệp vụ (contact spam, template không hợp lệ)"),
     *      @OA\Response(response=404, description="Không tìm thấy"),
     *      @OA\Response(response=422, description="Validation thất bại")
     * )
     */
    public function reply(ReplyContactRequest $request, int $id): JsonResponse
    {
        try {
            $data = $this->contactService->reply($id, $request->validated());

            return $this->sendResponse($data);
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }
}
