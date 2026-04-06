<?php

namespace App\Services\Admin;

use App\Helpers\ResponseHelper;
use App\Models\MailLog;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MailLogService extends BaseService
{
    protected $searchables = ['contact_id', 'to_email', 'subject'];

    protected $filterables = [
        'status' => 'filterByStatus',
    ];

    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
        'sent_at' => 'sent_at',
        'status' => 'status',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent|QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return MailLog::query()->with(['contact', 'mailTemplate']);
    }

    /**
     * filterByStatus
     *
     * @param  Eloquent|QueryBuilder $query
     * @param  array $filter
     * @return Eloquent|QueryBuilder
     */
    public function filterByStatus(Eloquent|QueryBuilder $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('status', (int) $filter['data']);
    }

    /**
     * detail
     *
     * @param  int $id
     * @return MailLog|array
     */
    public function detail(int $id): MailLog|array
    {
        $log = MailLog::with(['contact', 'mailTemplate'])->find($id);
        if (!$log) {
            return ResponseHelper::notFound('Lịch sử mail không tồn tại.');
        }

        return $log;
    }

    /**
     * retry
     *
     * @param  int $id
     * @return MailLog|array
     */
    public function retry(int $id): MailLog|array
    {
        try {
            $log = MailLog::find($id);
            if (!$log) {
                return ResponseHelper::notFound('Lịch sử mail không tồn tại.');
            }

            if ($log->status !== MailLog::STATUS_FAILED) {
                return ResponseHelper::sendError('Chỉ có thể gửi lại các email thất bại.');
            }

            $log->update([
                'status' => MailLog::STATUS_PENDING,
                'failed_reason' => null,
            ]);

            dispatch(new \App\Jobs\SendContactReplyMail($log));

            return $log->fresh();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
