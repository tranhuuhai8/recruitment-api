<?php

namespace App\Services\Applicant;

use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Models\ApplicationFile;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class FileUploadService extends BaseService
{
    /**
     * makeNewQuery
     *
     * @return Eloquent | QueryBuilder
     */
    public function makeNewQuery(): Eloquent|QueryBuilder
    {
        return ApplicationFile::query();
    }

    /**
     * list
     *
     * @return Collection
     */
    public function list(): Collection
    {
        return ApplicationFile::query()
            ->where('applicant_id', auth('applicant')->user()?->applicant?->id)
            ->orderBy('order')
            ->get();
    }

    /**
     * Method upsert
     *
     * @param array $data [explicite description]
     *
     * @return bool | array
     */
    public function upsert(array $data): bool|array
    {
        try {
            DB::beginTransaction();
            $maxFileUpload = config('length.max_file_upload');
            $applicantId = auth('applicant')->user()?->applicant?->id;
            $totalFileUploaded = ApplicationFile::query()
                ->where('applicant_id', $applicantId)
                ->count();

            if ($totalFileUploaded > $maxFileUpload) {
                return ResponseHelper::sendError(trans('validation.max.total_file_upload', ['max' => $maxFileUpload]));
            }

            $incomingIds = collect($data)->pluck('id')->filter()->toArray();
            $existingIds = ApplicationFile::where('applicant_id', $applicantId)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            if (count($idsToDelete)) {
                $this->deleteFile($idsToDelete);
            }

            ApplicationFile::upsert(
                $this->makeDataUpsert($data),
                ['id'],
                [
                    'file_name',
                    'file_path',
                    'file_type',
                    'file_size',
                    'order',
                    'applicant_id',
                    'created_at',
                    'updated_at'
                ],
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * deleteFile
     *
     * @param  array $ids
     * @return void
     */
    protected function deleteFile(array $ids)
    {
        $files = ApplicationFile::whereIn('id', $ids)->get();
        foreach ($files as $file) {
            if ($file->file_path) {
                StorageHelper::deleteFileByPath($file->file_path);
            }
        }

        ApplicationFile::whereIn('id', $ids)->forceDelete();
    }

    /**
     * Method makeDataUpsert
     *
     * @param array $data [explicite description]
     *
     * @return array
     */
    protected function makeDataUpsert(array $data): array
    {
        return collect($data)->map(function ($item) {
            unset($item['deleted_at']);
            unset($item['editing']);
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        })
            ->toArray();
    }
}
