<?php

namespace App\Services\Admin;

use App\Models\City;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\MailLog;
use App\Models\User;
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
        return Company::query();
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
        return [
            'totals' => $this->getDataTotal(),
            'chartTopCategory' => $this->getTopCategory($range, $from, $to),
            'chartJobApplication' => $this->getJobApplication($range, $from, $to),
            'chartUserGrowth' => $this->getUserGrowth($range, $from, $to),
            'chartJobStatus' => $this->getJobStatusBreakdown($range, $from, $to),
            'topCompanies' => $this->getTopCompanies(),
            'topCities' => $this->getTopCities(),
            'needsAttention' => $this->getNeedsAttention(),
        ];
    }

    /**
     * Method getDataTotal
     *
     * @return array
     */
    public function getDataTotal(): array
    {
        $users = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->where('role', '<>', User::ROLE_ADMIN)
            ->groupBy('role')
            ->pluck('total', 'role');

        $result = JobApplication::query()
            ->whereRelation('job', 'deleted_at', null)
            ->selectRaw("
                SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as accepted_count,
                SUM(CASE WHEN status IN (3, 4) THEN 1 ELSE 0 END) as finalized_count
            ")
            ->first();

        $rate = $result->finalized_count > 0
            ? round(($result->accepted_count / $result->finalized_count) * 100, 2)
            : 0;

        return [
            'company' => $users[User::ROLE_COMPANY] ?? 0,
            'applicant' => $users[User::ROLE_APPLICANT] ?? 0,
            'job' => Job::where('status', Job::STATUS_OPEN)->count(),
            'application_acceptance_rate' => $rate,
            'total_applications' => JobApplication::whereRelation('job', 'deleted_at', null)->count(),
        ];
    }

    /**
     * getTopCategory
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return \Illuminate\Support\Collection
     */
    public function getTopCategory(
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): \Illuminate\Support\Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        $query = JobCategory::query()
            ->select('job_categories.*')
            ->selectRaw('
        (
            SELECT COUNT(*)
            FROM jobs
            WHERE (
                jobs.job_category_id = job_categories.id
                OR jobs.job_category_id IN (
                    SELECT id
                    FROM job_categories child
                    WHERE child.parent_id = job_categories.id
                )
            )
            AND jobs.created_at BETWEEN ? AND ?
        ) as total_jobs
    ', [$startDate, $endDate])
            ->where([
                'status' => JobCategory::STATUS_SHOW,
                'parent_id' => null,
            ]);

        return DB::query()
            ->fromSub($query, 't')
            ->where('total_jobs', '>', 0)
            ->orderByDesc('total_jobs')
            ->limit(10)
            ->get();
    }

    /**
     * getJobApplication
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getJobApplication(?string $range = null, ?string $from = null, ?string $to = null): Collection
    {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return JobApplication::query()
            ->whereRelation('job', 'deleted_at', null)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * getUserGrowth
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return \Illuminate\Support\Collection
     */
    public function getUserGrowth(
        ?string $range = null,
        ?string $from = null,
        ?string $to = null
    ): \Illuminate\Support\Collection {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        $rows = User::query()
            ->selectRaw('DATE(created_at) as date, role, COUNT(*) as count')
            ->where('role', '<>', User::ROLE_ADMIN)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'role')
            ->orderBy('date')
            ->get();

        $pivoted = [];
        foreach ($rows as $row) {
            $pivoted[$row->date] ??= ['date' => $row->date, 'company' => 0, 'applicant' => 0];

            if ((int) $row->role === User::ROLE_COMPANY) {
                $pivoted[$row->date]['company'] = (int) $row->count;
            } elseif ((int) $row->role === User::ROLE_APPLICANT) {
                $pivoted[$row->date]['applicant'] = (int) $row->count;
            }
        }

        return collect(array_values($pivoted));
    }

    /**
     * getJobStatusBreakdown
     *
     * @param  string|null $range
     * @param  string|null $from
     * @param  string|null $to
     * @return Collection
     */
    public function getJobStatusBreakdown(?string $range = null, ?string $from = null, ?string $to = null): Collection
    {
        [$startDate, $endDate] = $this->resolveDateRange($range, $from, $to);

        return Job::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    /**
     * getTopCompanies
     *
     * @return Collection
     */
    public function getTopCompanies(): Collection
    {
        return Company::query()
            ->withCount(['jobs', 'followers'])
            ->orderByDesc('jobs_count')
            ->limit(5)
            ->get(['id', 'name', 'slug']);
    }

    /**
     * getTopCities
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopCities(): \Illuminate\Support\Collection
    {
        $query = City::query()
            ->select('cities.*')
            ->selectRaw('
        (
            SELECT COUNT(*)
            FROM jobs
            WHERE jobs.city_id = cities.id
            OR jobs.city_id IN (
                SELECT id
                FROM cities child
                WHERE child.parent_id = cities.id
            )
        ) as total_jobs
    ')
            ->where([
                'status' => City::STATUS_SHOW,
                'parent_id' => null,
            ]);

        return DB::query()
            ->fromSub($query, 't')
            ->where('total_jobs', '>', 0)
            ->orderByDesc('total_jobs')
            ->limit(5)
            ->get();
    }

    /**
     * getNeedsAttention
     *
     * @return array
     */
    public function getNeedsAttention(): array
    {
        return [
            'pending_contacts' => Contact::where('status', Contact::STATUS_NEW)->count(),
            'failed_mails' => MailLog::where('status', MailLog::STATUS_FAILED)->count(),
        ];
    }
}
