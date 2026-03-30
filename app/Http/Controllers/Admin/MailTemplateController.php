<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MailTemplate\CreateMailTemplateRequest;
use App\Http\Requests\Admin\MailTemplate\UpdateMailTemplateRequest;
use App\Http\Resources\Admin\MailTemplate\MailTemplateCollection;
use App\Http\Resources\Admin\MailTemplate\MailTemplateResource;
use App\Services\Admin\MailTemplateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailTemplateController extends BaseController
{
    public function __construct(
        private readonly MailTemplateService $mailTemplateService
    ) {
        $this->middleware($this->authMiddleware());
    }

    /**
     * @OA\Get(
     *      path="/api/admin/mail-template",
     *      operationId="adminGetMailTemplateList",
     *      tags={"Admin - Mail Template"},
     *      summary="Lấy danh sách mail template",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Tìm kiếm theo tên, code, tiêu đề",
     *          required=false,
     *          @OA\Schema(type="string", example="confirmation")
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
     *          description="Khóa lọc: type | is_active",
     *          required=false,
     *          @OA\Schema(type="string", example="type")
     *      ),
     *      @OA\Parameter(
     *          name="filters[0][data]",
     *          in="query",
     *          description="Giá trị lọc (type | is_active)",
     *          required=false,
     *          @OA\Schema(type="string", example="1")
     *      ),
     *
     *      @OA\Response(response=200, description="Thành công"),
     *      @OA\Response(response=401, description="Chưa xác thực")
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $data = $this->mailTemplateService::getInstance()->data(...$this->getParamRequest($request));

        return $this->sendSuccessResponse(new MailTemplateCollection($data));
    }

    /**
     * @OA\Post(
     *      path="/api/admin/mail-template",
     *      operationId="adminCreateMailTemplate",
     *      tags={"Admin - Mail Template"},
     *      summary="Tạo mail template mới",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "code", "subject", "body", "type"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="code", type="string"),
     *              @OA\Property(property="subject", type="string"),
     *              @OA\Property(property="body", type="string"),
     *              @OA\Property(property="variables", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="type", type="integer"),
     *              @OA\Property(property="is_active", type="boolean")
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Tạo thành công"),
     *      @OA\Response(response=422, description="Validation thất bại")
     * )
     */
    public function store(CreateMailTemplateRequest $request): JsonResponse
    {
        try {
            $data = $this->mailTemplateService->store($request->validated());

            return $this->sendResponse($data, 'create');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/admin/mail-template/{id}",
     *      operationId="adminGetMailTemplateDetail",
     *      tags={"Admin - Mail Template"},
     *      summary="Xem chi tiết mail template",
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
        $data = $this->mailTemplateService->detail($id);
        if (is_array($data)) {
            return $this->sendResponse($data);
        }

        return $this->sendSuccessResponse(new MailTemplateResource($data));
    }

    /**
     * @OA\Put(
     *      path="/api/admin/mail-template/{id}",
     *      operationId="adminUpdateMailTemplate",
     *      tags={"Admin - Mail Template"},
     *      summary="Cập nhật mail template",
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
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="subject", type="string"),
     *              @OA\Property(property="body", type="string"),
     *              @OA\Property(property="variables", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="type", type="integer"),
     *              @OA\Property(property="is_active", type="boolean")
     *          )
     *      ),
     *
     *      @OA\Response(response=200, description="Cập nhật thành công"),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function update(UpdateMailTemplateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $this->mailTemplateService->update($request->validated(), $id);

            return $this->sendResponse($data, 'update');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/admin/mail-template/{id}",
     *      operationId="adminDeleteMailTemplate",
     *      tags={"Admin - Mail Template"},
     *      summary="Xóa mail template (soft delete)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\Response(response=200, description="Xóa thành công"),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $data = $this->mailTemplateService->delete($id);

            return $this->sendResponse($data, 'delete');
        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *      path="/api/admin/mail-template/{id}/preview",
     *      operationId="adminPreviewMailTemplate",
     *      tags={"Admin - Mail Template"},
     *      summary="Preview template với dữ liệu mẫu",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Preview thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="subject", type="string", example="Chào Nguyễn Văn A, ..."),
     *                  @OA\Property(property="body", type="string", example="<p>Nội dung đã render...</p>"),
     *                  @OA\Property(property="variables", type="array", @OA\Items(type="string"))
     *              )
     *          )
     *      ),
     *      @OA\Response(response=404, description="Không tìm thấy")
     * )
     */
    public function preview(int $id): JsonResponse
    {
        $data = $this->mailTemplateService->preview($id);

        return $this->sendResponse($data);
    }
}
