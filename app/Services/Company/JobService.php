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
     * @param int $id [explicite description]
     *
     * @return Job | array
     */
    public function detail(int $id): Job|array
    {
        try {
            $job = Job::with('city')->find($id);
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

            // Notify applicants who followed this company (only when job is open)
            if ($job && (int) $job->status === Job::STATUS_OPEN) {
                $company = $job->company()->with('user')->first();
                if ($company) {
                    $frontendBase = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3024'));
                    $jobUrl = rtrim($frontendBase, '/') . '/page-job/' . $job->id;

                    SendNewJobToFollowers::dispatch(
                        $company->id,
                        ['id' => $company->id, 'name' => $company->name],
                        ['id' => $job->id, 'title' => $job->title, 'end_date' => $job->end_date],
                        $jobUrl,
                    );
                }
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
     * @param  int $id
     * @return bool | array
     */
    public function update(array $data, int $id): bool|array
    {
        try {
            $job = Job::find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job->update($data);
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
            $job = Job::find($id);
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
