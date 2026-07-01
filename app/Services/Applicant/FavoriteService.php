<?php

namespace App\Services\Applicant;

use App\Helpers\ResponseHelper;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\JobFavorite;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FavoriteService
{
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
