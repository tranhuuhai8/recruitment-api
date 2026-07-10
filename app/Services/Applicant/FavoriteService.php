<?php

namespace App\Services\Applicant;

use App\Helpers\ResponseHelper;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobFavorite;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class FavoriteService extends BaseService
{
    protected $orderables = [
        'id' => 'id',
        'created_at' => 'created_at',
    ];

    protected $searchables = [
        'title' => 'job.title',
        'name' => 'job.company.name',
    ];

    protected $searchableRelations = true;

    protected $filterables = [
        'is_applied' => 'filterByApplied',
    ];

    /**
     * Method getCurrentApplicant
     *
     * @return Applicant|null
     */
    protected function getCurrentApplicant(): ?Applicant
    {
        $user = auth('api')->user();

        return $user?->applicant;
    }

    /**
     * filterByApplied
     *
     * @param  Eloquent $query
     * @param  array $filter
     * @return Eloquent|QueryBuilder
     */
    public function filterByApplied(Eloquent $query, array $filter): Eloquent|QueryBuilder
    {
        if (!isset($filter['data']) || !$filter['data']) {
            return $query;
        }

        $applicant = $this->getCurrentApplicant();
        $method = (int) $filter['data'] === 1 ? 'whereHas' : 'whereDoesntHave';

        return $query->{$method}('job.applications', function ($q) use ($applicant) {
            $q->where('applicant_id', $applicant->id);
        });
    }

    /**
     * makeNewQuery
     *
     * @return Eloquent|QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return JobFavorite::query()->whereRaw('1 = 0');
        }

        return JobFavorite::query()
            ->with([
                'job.company',
                'job.applications' => function ($query) use ($applicant) {
                    $query->where('applicant_id', $applicant->id);
                },
            ])
            ->whereHas('job', function ($query) {
                $query->whereNull('deleted_at')
                    ->where('status', Job::STATUS_OPEN);
            })
            ->where('applicant_id', $applicant->id)
            ->orderByDesc('id');
    }

    /**
     * listJobs
     *
     * @return array|Collection
     */
    public function listJobs(): array|Collection
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return ResponseHelper::sendError('Applicant not found');
        }

        $favorites = JobFavorite::query()
            ->with('job:id,slug')
            ->where('applicant_id', $applicant->id)
            ->whereNull('deleted_at')
            ->whereRelation('job', 'deleted_at', null)
            ->orderByDesc('id')
            ->get();

        return [
            'job_ids' => $favorites->pluck('job_id'),
            'job_slugs' => $favorites->pluck('job.slug')->filter()->values(),
        ];
    }

    /**
     * toggleJob
     *
     * @param  string $slug
     * @return array
     */
    public function toggleJob(string $slug): array
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return ResponseHelper::sendError('Applicant not found');
        }

        $job = Job::query()->whereNull('deleted_at')->where('slug', $slug)->first();
        if (!$job) {
            return ResponseHelper::notFound('Job not found');
        }

        $jobId = $job->id;
        $saved = DB::transaction(function () use ($applicant, $jobId) {
            $favorite = JobFavorite::withTrashed()
                ->where('applicant_id', $applicant->id)
                ->where('job_id', $jobId)
                ->first();

            if (!$favorite) {
                JobFavorite::create([
                    'applicant_id' => $applicant->id,
                    'job_id' => $jobId,
                    'note' => null,
                ]);
                return true;
            }

            if ($favorite->trashed()) {
                $favorite->restore();
                return true;
            }

            $favorite->delete();
            return false;
        });

        return ['saved' => $saved];
    }
}
