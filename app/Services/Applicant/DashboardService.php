<?php

namespace App\Services\Applicant;

use App\Models\CompanyFollower;
use App\Models\JobApplication;
use App\Models\JobFavorite;
use App\Services\BaseService;
use App\Services\Concerns\ResolvesDashboardDateRange;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService extends BaseService
{
    use ResolvesDashboardDateRange;

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return JobApplication::query();
    }

    /**
     * list
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return array
     */
    public function list(?string $range = null, ?string $from = null, ?string $to = null): array
    {
        $applicantId = auth('api')->user()?->applicant?->id;

        if (!$applicantId) {
            return [
                'totals' => $this->emptyTotals(),
                'chartApplicationByDay' => [],
                'chartApplicationStatus' => [],
                'recentApplications' => [],
            ];
        }

        return [
            'totals' => $this->getDataTotal($applicantId),
            'chartApplicationByDay' => $this->getApplicationByDay($applicantId, $range, $from, $to),
            'chartApplicationStatus' => $this->getApplicationStatusBreakdown($applicantId, $range, $from, $to),
            'recentApplications' => $this->getRecentApplications($applicantId),
        ];
    }

    /**
     * emptyTotals
     *
     * @return array
     */
    private function emptyTotals(): array
    {
        return [
            'total_applications' => 0,
            'pending' => 0,
            'accepted' => 0,
            'favorites' => 0,
            'follows' => 0,
        ];
    }

    /**
     * getDataTotal
     *
     * @param  int $applicantId
     * @return array
     */
    public function getDataTotal(int $applicantId): array
    {
        return [
            'total_applications' => $this->activeApplicationsQuery($applicantId)->count(),
            'pending' => $this->activeApplicationsQuery($applicantId)
                ->where('status', JobApplication::STATUS_PENDING)
                ->count(),
            'accepted' => $this->activeApplicationsQuery($applicantId)
                ->where('status', JobApplication::STATUS_ACCEPTED)
                ->count(),
            'favorites' => JobFavorite::where('applicant_id', $applicantId)
                ->whereRelation('job', 'deleted_at', null)
                ->count(),
            'follows' => CompanyFollower::where('applicant_id', $applicantId)
                ->whereRelation('company', 'deleted_at', null)
                ->count(),
        ];
    }

    /**
     * getApplicationByDay
     *
     * @param  int $applicantId
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getApplicationByDay(
        int $applicantId,
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return $this->activeApplicationsQuery($applicantId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * getApplicationStatusBreakdown
     *
     * @param  int $applicantId
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getApplicationStatusBreakdown(
        int $applicantId,
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return $this->activeApplicationsQuery($applicantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    /**
     * getRecentApplications
     *
     * @param  int $applicantId
     * @return \Illuminate\Support\Collection
     */
    public function getRecentApplications(int $applicantId): \Illuminate\Support\Collection
    {
        return $this->activeApplicationsQuery($applicantId)
            ->with('job.company')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (JobApplication $application) => [
                'job_id' => $application->job_id,
                'job_title' => $application->job?->title,
                'company_name' => $application->job?->company?->name,
                'status' => $application->status,
                'created_at' => $application->created_at,
            ]);
    }

    /**
     * activeApplicationsQuery
     *
     * Scopes JobApplication rows to the given applicant, excluding
     * applications whose job has since been deleted.
     *
     * @param  int $applicantId
     * @return Eloquent
     */
    private function activeApplicationsQuery(int $applicantId): Eloquent
    {
        return JobApplication::query()
            ->where('applicant_id', $applicantId)
            ->whereRelation('job', 'deleted_at', null);
    }
}
