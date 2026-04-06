<?php

namespace App\Services\Admin;

use App\Helpers\DateHelper;
use App\Helpers\ResponseHelper;
use App\Jobs\SendContactConfirmationMail;
use App\Jobs\SendContactReplyMail;
use App\Models\Contact;
use App\Models\MailLog;
use App\Models\MailTemplate;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class ContactService extends BaseService
{
    protected $searchables = ['full_name', 'email', 'title', 'content'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'priority' => 'filterByPriority',
    ];

    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
        'priority' => 'priority',
        'status' => 'status',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent|QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Contact::query();
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
     * filterByPriority
     *
     * @param  Eloquent|QueryBuilder $query
     * @param  array $filter
     * @return Eloquent|QueryBuilder
     */
    public function filterByPriority(Eloquent|QueryBuilder $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('priority', (int) $filter['data']);
    }

    /**
     * store
     *
     * @param  array $data
     * @return Contact|array
     */
    public function store(array $data): Contact|array
    {
        try {
            DB::beginTransaction();

            $contact = Contact::create([
                ...$data,
                'status' => Contact::STATUS_NEW,
                'priority' => Contact::PRIORITY_NORMAL,
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            dispatch(new SendContactConfirmationMail($contact));

            return $contact;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Gửi tin nhắn thất bại: ' . $e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  array $data
     * @param  int   $id
     * @return Contact|array
     */
    public function update(array $data, int $id): Contact|array
    {
        try {
            $contact = Contact::find($id);
            if (!$contact) {
                return ResponseHelper::notFound();
            }

            if ($contact->status === Contact::STATUS_NEW && !isset($data['status'])) {
                $data['status'] = Contact::STATUS_READ;
            }

            $contact->update($data);

            return $contact->fresh();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * detail
     *
     * @param  int $id
     * @return Contact|array
     */
    public function detail(int $id): Contact|array
    {
        $contact = Contact::with('mailLogs.mailTemplate')->find($id);
        if (!$contact) {
            return ResponseHelper::notFound();
        }

        if ($contact->status === Contact::STATUS_NEW) {
            $contact->update(['status' => Contact::STATUS_READ]);
        }

        return $contact->fresh(['mailLogs.mailTemplate']);
    }

    /**
     * reply
     *
     * @param  int   $id
     * @param  array $data
     * @return MailLog|array
     */
    public function reply(int $id, array $data): MailLog|array
    {
        try {
            $contact = Contact::find($id);
            if (!$contact) {
                return ResponseHelper::notFound();
            }

            if ($contact->status === Contact::STATUS_SPAM) {
                return ResponseHelper::sendError('Không thể phản hồi tin nhắn Spam.');
            }

            $template = MailTemplate::find($data['mail_template_id']);
            if (!$template || !$template->is_active) {
                return ResponseHelper::sendError('Template email không tồn tại hoặc đã bị vô hiệu hóa.');
            }

            DB::beginTransaction();

            $subject = $data['subject'] ?? $this->renderTemplate($template->subject, $contact);
            $body = $data['body'] ?? $this->renderTemplate($template->body, $contact);

            $mailLog = MailLog::create([
                'contact_id' => $contact->id,
                'mail_template_id' => $template->id,
                'from_email' => config('mail.from.address'),
                'to_email' => $contact->email,
                'to_name' => $contact->full_name,
                'subject' => $subject,
                'body' => $body,
                'status' => MailLog::STATUS_PENDING,
            ]);

            $contact->update(['status' => Contact::STATUS_IN_PROGRESS]);
            DB::commit();
            dispatch(new SendContactReplyMail($mailLog));

            return $mailLog;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Gửi phản hồi thất bại: ' . $e->getMessage());
        }
    }

    /**
     * renderTemplate
     *
     * @param  string  $template
     * @param  Contact $contact
     * @return string
     */
    public function renderTemplate(string $template, Contact $contact): string
    {
        $variables = [
            '{{full_name}}' => $contact->full_name,
            '{{email}}' => $contact->email,
            '{{phone}}' => $contact->phone ?? 'N/A',
            '{{title}}' => $contact->title,
            '{{content}}' => $contact->content,
            '{{created_at}}' => DateHelper::parseDateBe($contact->created_at),
            '{{app_name}}' => config('app.name'),
            '{{app_url}}' => config('app.url_home'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }
}
