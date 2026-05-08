<?php

namespace App\Http\Controllers\Applicant;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Company;
use App\Models\CompanyFollower;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CompanyFollowController extends Controller
{
    public function listCompanies(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $applicant = $user?->applicant;
        if (!$applicant) {
            return $this->sendErrorResponse('Applicant not found', null, ResponseHelper::STATUS_CODE_BAD_REQUEST);
        }

        $companyIds = CompanyFollower::query()
            ->where('applicant_id', $applicant->id)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->pluck('company_id');

        return $this->sendSuccessResponse([
            'company_ids' => $companyIds,
        ]);
    }

    public function toggleCompany(int $companyId): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        /** @var Applicant|null $applicant */
        $applicant = $user?->applicant;
        if (!$applicant) {
            return $this->sendErrorResponse('Applicant not found', null, ResponseHelper::STATUS_CODE_BAD_REQUEST);
        }

        $company = Company::query()->whereNull('deleted_at')->find($companyId);
        if (!$company) {
            return $this->sendErrorResponse('Company not found', null, ResponseHelper::STATUS_CODE_NOT_FOUND);
        }

        $followed = DB::transaction(function () use ($applicant, $companyId) {
            $follow = CompanyFollower::withTrashed()
                ->where('applicant_id', $applicant->id)
                ->where('company_id', $companyId)
                ->first();

            if (!$follow) {
                CompanyFollower::create([
                    'applicant_id' => $applicant->id,
                    'company_id' => $companyId,
                ]);
                return true;
            }

            if ($follow->trashed()) {
                $follow->restore();
                return true;
            }

            $follow->delete();
            return false;
        });

        return $this->sendSuccessResponse(['followed' => $followed]);
    }
}

