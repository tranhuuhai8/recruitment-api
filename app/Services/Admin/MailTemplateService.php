<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\MailTemplate;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class MailTemplateService extends BaseService
{
    protected $searchables = ['name', 'code', 'subject'];

    protected $filterables = [
        'type'      => 'filterByType',
        'is_active' => 'filterByActive',
    ];

    protected $orderables = [
        'id'         => 'id',
        'name'       => 'name',
        'created_at' => 'created_at',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent|QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return MailTemplate::query();
    }

    /**
     * filterByType
     *
     * @param  Eloquent|QueryBuilder $query
     * @param  array $filter
     * @return Eloquent|QueryBuilder
     */
    public function filterByType(Eloquent|QueryBuilder $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('type', (int) $filter['data']);
    }

    /**
     * filterByActive
     *
     * @param  Eloquent|QueryBuilder $query
     * @param  array $filter
     * @return Eloquent|QueryBuilder
     */
    public function filterByActive(Eloquent|QueryBuilder $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || $filter['data'] === '') {
            return $query;
        }

        return $query->where('is_active', (bool) $filter['data']);
    }

    /**
     * store
     * Tạo mail template mới
     *
     * @param  array $data
     * @return MailTemplate|array
     */
    public function store(array $data): MailTemplate|array
    {
        try {
            DB::beginTransaction();
            $template = MailTemplate::create($data);
            DB::commit();

            return $template;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Tạo template thất bại: ' . $e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  array $data
     * @param  int   $id
     * @return MailTemplate|array
     */
    public function update(array $data, int $id): MailTemplate|array
    {
        try {
            $template = MailTemplate::find($id);
            if (!$template) {
                return ResponseHelper::notFound();
            }

            $template->update($data);

            return $template->fresh();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * delete
     *
     * @param  int $id
     * @return bool|array
     */
    public function delete(int $id): bool|array
    {
        try {
            $template = MailTemplate::find($id);
            if (!$template) {
                return ResponseHelper::notFound();
            }

            return (bool) $template->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * detail
     *
     * @param  int $id
     * @return MailTemplate|array
     */
    public function detail(int $id): MailTemplate|array
    {
        $template = MailTemplate::find($id);
        if (!$template) {
            return ResponseHelper::notFound();
        }

        return $template;
    }

    /**
     * preview
     *
     * @param  int $id
     * @return array
     */
    public function preview(int $id): array
    {
        $template = MailTemplate::find($id);
        if (!$template) {
            return ResponseHelper::notFound();
        }

        $sampleData = [
            '{{full_name}}' => 'Nguyễn Văn A',
            '{{email}}'     => 'example@email.com',
            '{{phone}}'     => '0901234567',
            '{{title}}'     => 'Tôi cần hỗ trợ về tài khoản',
            '{{content}}'   => 'Nội dung tin nhắn mẫu...',
            '{{created_at}}' => now()->format('d/m/Y H:i'),
            '{{app_name}}'  => config('app.name'),
            '{{app_url}}'   => config('app.url_home'),
        ];

        return [
            'id'        => $template->id,
            'code'      => $template->code,
            'subject'   => str_replace(array_keys($sampleData), array_values($sampleData), $template->subject),
            'body'      => str_replace(array_keys($sampleData), array_values($sampleData), $template->body),
            'variables' => $template->variables,
        ];
    }
}
