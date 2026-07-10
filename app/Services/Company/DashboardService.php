<?php

namespace App\Services\Company;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
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
        return Job::query();
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
        $companyId = auth('api')->user()?->company?->id;

        if (!$companyId) {
            return [
                'totals' => $this->emptyTotals(),
                'chartApplicationByDay' => [],
                'chartApplicationStatus' => [],
                'topJobs' => [],
            ];
        }

        return [
            'totals' => $this->getDataTotal($companyId),
            'chartApplicationByDay' => $this->getApplicationByDay($companyId, $range, $from, $to),
            'chartApplicationStatus' => $this->getApplicationStatusBreakdown($companyId, $range, $from, $to),
            'topJobs' => $this->getTopJobs($companyId),
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
            'open_jobs' => 0,
            'total_applications' => 0,
            'pending_applications' => 0,
            'followers' => 0,
        ];
    }

    /**
     * getDataTotal
     *
     * @param  int $companyId
     * @return array
     */
    public function getDataTotal(int $companyId): array
    {
        $company = Company::withCount('followers')->find($companyId);

        return [
            'open_jobs' => Job::where('company_id', $companyId)->where('status', Job::STATUS_OPEN)->count(),
            'total_applications' => JobApplication::whereRelation('job', 'company_id', $companyId)->count(),
            'pending_applications' => JobApplication::whereRelation('job', 'company_id', $companyId)
                ->where('status', JobApplication::STATUS_PENDING)
                ->count(),
            'followers' => $company->followers_count ?? 0,
        ];
    }

    /**
     * getApplicationByDay
     *
     * @param  int $companyId
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getApplicationByDay(
        int $companyId,
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return JobApplication::query()
            ->whereRelation('job', 'company_id', $companyId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * getApplicationStatusBreakdown
     *
     * @param  int $companyId
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getApplicationStatusBreakdown(
        int $companyId,
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return JobApplication::query()
            ->whereRelation('job', 'company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    /**
     * getTopJobs
     *
     * @param  int $companyId
     * @return Collection
     */
    public function getTopJobs(int $companyId): Collection
    {
        return Job::query()
            ->where('company_id', $companyId)
            ->withCount([
                'applications',
                'applications as accepted_count' => function ($query) {
                    $query->where('status', JobApplication::STATUS_ACCEPTED);
                },
            ])
            ->orderByDesc('applications_count')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'number_of_recruitment']);
    }
}
