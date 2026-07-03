<?php

namespace App\Services\Company;

use App\Helpers\ResponseHelper;
use App\Jobs\SendNewJobToFollowers;
use App\Models\Job;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;

class JobService extends BaseService
{
    use CompanyTrait;

    protected $orderables = [
        'id' => 'id',
        'title' => 'title',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
    ];

    protected $searchables = ['title'];

    protected $filterables = [
        'status' => 'filterByStatus',
        'type' => 'filterByType',
        'city_id' => 'filterByCity',
        'job_category_id' => 'filterByJobCategory',
        'start_date' => 'filterByStartDate',
        'end_date' => 'filterByEndDate',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Job::query()
            ->with(['city.parent', 'jobCategory.parent'])
            ->whereRelation('company', 'user_id', auth('api')->id());
    }

    /**
     * Method detail
     *
     * @param string $slug [explicite description]
     *
     * @return Job | array
     */
    public function detail(string $slug): Job|array
    {
        try {
            $job = Job::with('city')->where('slug', $slug)->first();
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * store
     *
     * @param  array $data
     * @return Job | array
     */
    public function store(array $data): Job|array
    {
        try {
            $job = Job::create($data);

            if ($job && (int) $job->status === Job::STATUS_OPEN) {
                $this->notifyFollowers($job);
            }

            return $job;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * update
     *
     * @param  array $data
     * @param  string $slug
     * @return bool | array
     */
    public function update(array $data, string $slug): bool|array
    {
        try {
            $job = Job::where('slug', $slug)->first();
            if (!$job) {
                return ResponseHelper::notFound();
            }

            $previousStatus = (int) $job->status;
            $result = $job->update($data);

            if ($result && $previousStatus !== Job::STATUS_OPEN && (int) $job->status === Job::STATUS_OPEN) {
                $this->notifyFollowers($job);
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Notify applicants who followed this job's company that the job is now open.
     *
     * @param  Job $job
     * @return void
     */
    private function notifyFollowers(Job $job): void
    {
        $company = $job->company()->with('user')->first();
        if (!$company) {
            return;
        }

        $frontendBase = config('app.url_home');
        $jobUrl = rtrim($frontendBase, '/') . '/viec-lam/' . $job->slug;

        SendNewJobToFollowers::dispatch(
            $company->id,
            ['id' => $company->id, 'name' => $company->name],
            ['id' => $job->id, 'title' => $job->title, 'end_date' => $job->end_date],
            $jobUrl,
        );
    }

    /**
     * delete
     *
     * @param  string $slug
     * @return bool | array
     */
    public function delete(string $slug): bool|array
    {
        try {
            $job = Job::where('slug', $slug)->first();
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
