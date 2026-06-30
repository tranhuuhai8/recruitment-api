<?php

namespace App\Services\Home;

use App\Helpers\ResponseHelper;
use App\Models\ApplicationFile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class JobService extends BaseService
{
    use HomeTrait;

    protected $searchables = ['title'];

    protected $filterables = [
        'city_id' => 'filterByCity',
        'company_id' => 'filterByCompany',
        'job_category_id' => 'filterByJobCategory',
    ];

    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return Job::with(['company', 'city.parent', 'jobCategory.parent'])
            ->where('status', Job::STATUS_OPEN)
            ->orderByDesc('end_date');
    }

    /**
     * Method detail
     *
     * @param string $slug [explicite description]
     *
     * @return Job | array
     */
    public function detail(string $slug): Job|array
    {
        try {
            $job = Job::with(['company.city', 'company.user', 'city.parent', 'jobCategory.parent'])
                ->where('status', Job::STATUS_OPEN)
                ->where('slug', $slug)
                ->first();
            if (!$job) {
                return ResponseHelper::notFound();
            }

            return $job;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method apply
     *
     * @param array $data [explicite description]
     *
     * @return bool | array
     */
    public function apply(array $data): bool|array
    {
        try {
            DB::beginTransaction();
            $applicantId = data_get($data, 'applicant_id');

            $isApplied = JobApplication::query()
                ->where('job_id', $data['job_id'])
                ->where(function ($q) use ($data, $applicantId) {
                    if ($applicantId) {
                        $q->where('applicant_id', $applicantId);
                    } else {
                        $q->whereNull('applicant_id')
                            ->where(function ($sub) use ($data) {
                                $sub->where('guest_email', $data['guest_email'])
                                    ->orWhere('guest_telephone', $data['guest_telephone']);
                            });
                    }
                })
                ->first();

            if ($isApplied) {
                return ResponseHelper::sendError(trans('response.job.applied'));
            }

            $totalFileUploaded = ApplicationFile::query()
                ->where('applicant_id', $applicantId)
                ->count();

            if ($data['source_cv'] == 'upload') {
                unset($data['application_file_id']);
                if ($totalFileUploaded === config('length.max_file_upload')) {
                    return ResponseHelper::sendError(trans('response.job.max_file'));
                }
            }


            if ($applicantId) {
                $data['application_file_id'] = $this->createApplicationFile($data);
                $data['order'] = $totalFileUploaded + 1;
                unset(
                    $data['file_name'],
                    $data['file_path'],
                    $data['guest_name'],
                    $data['guest_email'],
                    $data['guest_telephone']
                );
            }

            JobApplication::create($data);
            // send mail to company

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method createApplicationFile
     *
     * @param array $data [explicite description]
     *
     * @return int
     */
    public function createApplicationFile(array $data): int
    {
        if (data_get($data, 'application_file_id')) {
            return (int) $data['application_file_id'];
        }

        $applicationFile = ApplicationFile::create($this->makeDataApplicationFile($data));

        return $applicationFile->id;
    }

    /**
     * Method getCv
     *
     * @return Collection | null
     */
    public function getCv(): Collection|null
    {
        try {
            $user = auth('api')?->user();
            return ApplicationFile::query()
                ->whereRelation('applicant.user', 'id', $user->id)
                ->take(ApplicationFile::TAKE_RECENTLY_UPLOADED)
                ->orderByDesc('created_at')
                ->get();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Method makeDataApplicationFile
     *
     * @param array $data [explicite description]
     *
     * @return array
     */
    protected function makeDataApplicationFile(array $data): array
    {
        return [
            'order' => data_get($data, 'order'),
            'applicant_id' => data_get($data, 'applicant_id'),
            'file_name' => data_get($data, 'file_name'),
            'file_path' => data_get($data, 'file_path'),
            'file_size' => data_get($data, 'file_size'),
            'file_type' => 'pdf',
        ];
    }
}
