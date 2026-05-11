<?php

namespace App\Services\Admin;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService extends BaseService
{
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
     * @return array
     */
    public function list(): array
    {
        return [
            'totals' => $this->getDataTotal(),
            'chartTopCategory' => $this->getTopCategory(),
            'chartJobApplication' => $this->getJobApplication(),
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
        ];
    }

    /**
     * getTopCategory
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopCategory(): \Illuminate\Support\Collection
    {
        $query = JobCategory::query()
            ->select('job_categories.*')
            ->selectRaw('
        (
            SELECT COUNT(*)
            FROM jobs 
            WHERE jobs.job_category_id = job_categories.id
            OR jobs.job_category_id IN (
                SELECT id
                FROM job_categories child
                WHERE child.parent_id = job_categories.id
            )
        ) as total_jobs
    ')
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
     * @return Collection
     */
    public function getJobApplication(): Collection
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        return JobApplication::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
