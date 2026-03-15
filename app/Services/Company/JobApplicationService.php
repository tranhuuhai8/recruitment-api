<?php

namespace App\Services\Company;

use App\Helpers\ResponseHelper;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobApplicationService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $searchables = [
        'title' => 'job.title',
        'name' => 'applicant.name',
        'telephone' => 'applicant.telephone',
        'mail_address' => 'applicant.user.mail_address',
        'guest_telephone' => 'guest_telephone',
        'guest_name' => 'guest_name',
        'guest_email' => 'guest_email',
    ];

    protected $searchableRelations = true;

    protected $filterables = [
        'job_id' => 'filterByJobId',
        'status' => 'filterByStatus',
        'created_at' => 'filterByDate',
    ];

    /**
     * filterByJobId
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByJobId(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('job_id', +$filter['data']);
    }

    /**
     * filterByStatus
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent
     */
    public function filterByStatus(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->where('status', +$filter['data']);
    }

    /**
     * Method filterByDate
     *
     * @param Eloquent $query [explicite description]
     * @param array $filter [explicite description]
     *
     * @return Eloquent
     */
    public function filterByDate(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        return $query->whereDate('created_at', $filter['data']);
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        $companyId = auth('api')->user()?->company?->id;

        if (!$companyId) {
            return JobApplication::query()->whereRaw('1 = 0');
        }

        return JobApplication::query()
            ->with(['applicant.user', 'applicationFile', 'job'])
            ->whereRelation('job', 'company_id', $companyId);
    }

    /**
     * update
     *
     * @param  array $data
     * @param  int $id
     * @return bool | array
     */
    public function update(array $data, int $id): bool|array
    {
        try {
            $jobApplication = JobApplication::find($id);
            if (!$jobApplication) {
                return ResponseHelper::notFound();
            }

            if ($data['status'] == JobApplication::STATUS_ACCEPTED) {
                $job = Job::withCount([
                    'applications as total_approved' => function ($query) {
                        $query->where('status', JobApplication::STATUS_ACCEPTED);
                    }
                ])->find($jobApplication->job_id);

                if ($job->total_approved === $job->number_of_recruitment) {
                    return ResponseHelper::sendError(trans('response.job.limit_recruitment_reached'));
                }
            }

            return $jobApplication->update($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * delete
     *
     * @param  int $id
     * @return bool | array
     */
    public function delete(int $id): bool|array
    {
        try {
            $jobApplication = JobApplication::find($id);
            if (!$jobApplication) {
                return ResponseHelper::notFound();
            }

            return $jobApplication->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
