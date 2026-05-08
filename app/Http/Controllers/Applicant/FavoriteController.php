<?php

namespace App\Http\Controllers\Applicant;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function listJobs(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $applicant = $user?->applicant;
        if (!$applicant) {
            return $this->sendErrorResponse('Applicant not found', null, ResponseHelper::STATUS_CODE_BAD_REQUEST);
        }

        $jobIds = JobFavorite::query()
            ->where('applicant_id', $applicant->id)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->pluck('job_id');

        return $this->sendSuccessResponse([
            'job_ids' => $jobIds,
        ]);
    }

    public function toggleJob(int $jobId): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        /** @var Applicant|null $applicant */
        $applicant = $user?->applicant;
        if (!$applicant) {
            return $this->sendErrorResponse('Applicant not found', null, ResponseHelper::STATUS_CODE_BAD_REQUEST);
        }

        $job = Job::query()->whereNull('deleted_at')->find($jobId);
        if (!$job) {
            return $this->sendErrorResponse('Job not found', null, ResponseHelper::STATUS_CODE_NOT_FOUND);
        }

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

        return $this->sendSuccessResponse(['saved' => $saved]);
    }
}

