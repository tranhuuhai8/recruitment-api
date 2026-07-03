<?php

namespace App\Services\Applicant;

use App\Helpers\ResponseHelper;
use App\Models\Applicant;
use App\Models\Company;
use App\Models\CompanyFollower;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class CompanyFollowService extends BaseService
{
    protected $searchables = ['companies.name', 'companies.short_name'];

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
     * makeNewQuery
     *
     * @return Eloquent|QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return Company::query()->whereRaw('1 = 0');
        }

        return Company::query()
            ->select('companies.*', 'cf.notify_new_job')
            ->join('company_followers as cf', function ($join) use ($applicant) {
                $join->on('cf.company_id', '=', 'companies.id')
                    ->where('cf.applicant_id', $applicant->id)
                    ->whereNull('cf.deleted_at');
            })
            ->with('city.parent')
            ->withCount('jobs')
            ->whereNull('companies.deleted_at')
            ->orderByDesc('cf.id');
    }

    /**
     * listCompanies
     *
     * @return array
     */
    public function listCompanies(): array
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return ResponseHelper::sendError('Applicant not found');
        }

        $companyIds = CompanyFollower::query()
            ->where('applicant_id', $applicant->id)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->pluck('company_id');

        return ['company_ids' => $companyIds];
    }

    /**
     * toggleCompany
     *
     * @param  string $slug
     * @return array
     */
    public function toggleCompany(string $slug): array
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return ResponseHelper::sendError('Applicant not found');
        }

        $company = Company::query()->whereNull('deleted_at')->where('slug', $slug)->first();
        if (!$company) {
            return ResponseHelper::notFound('Company not found');
        }

        $companyId = $company->id;
        $followed = DB::transaction(function () use ($applicant, $companyId) {
            $follow = CompanyFollower::withTrashed()
                ->where([
                    'applicant_id' => $applicant->id,
                    'company_id' => $companyId
                ])
                ->first();

            if (!$follow) {
                CompanyFollower::create([
                    'applicant_id' => $applicant->id,
                    'company_id' => $companyId,
                    'notify_new_job' => true,
                ]);
                return true;
            }

            if ($follow->trashed()) {
                $follow->restore();
                $follow->update(['notify_new_job' => true]);
                return true;
            }

            $follow->delete();
            return false;
        });

        return ['followed' => $followed];
    }

    /**
     * toggleNotify
     *
     * @param  string $slug
     * @return array
     */
    public function toggleNotify(string $slug): array
    {
        $applicant = $this->getCurrentApplicant();
        if (!$applicant) {
            return ResponseHelper::sendError('Applicant not found');
        }

        $company = Company::query()->whereNull('deleted_at')->where('slug', $slug)->first();
        if (!$company) {
            return ResponseHelper::notFound('Company not found');
        }

        $follow = CompanyFollower::query()
            ->where([
                'applicant_id' => $applicant->id,
                'company_id' => $company->id
            ])
            ->whereNull('deleted_at')
            ->first();

        if (!$follow) {
            return ResponseHelper::sendError('Bạn chưa theo dõi công ty này');
        }

        $follow->update(['notify_new_job' => !$follow->notify_new_job]);

        return ['notify_new_job' => $follow->notify_new_job];
    }
}
