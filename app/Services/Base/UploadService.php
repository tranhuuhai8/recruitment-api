<?php

namespace App\Services\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = Storage::getDefaultDriver();
    }

    /**
     * Method uploadImg
     *
     * @param Request $request [explicite description]
     *
     * @return array
     */
    public function uploadImg(Request $request): array
    {
        $file = $request->file('file');
        $filePath = $file->store('attachments', $this->disk);

        return [
            'url' => Storage::disk($this->disk)->url($filePath),
            'file_path' => $filePath,
        ];
    }

    /**
     * Method uploadPdf
     *
     * @param Request $request [explicite description]
     * @param string $disk [explicite description]
     *
     * @return array
     */
    public function uploadPdf(Request $request): array
    {
        $file = $request->file('file');
        $filePath = $file->store('pdfs', $this->disk);

        return [
            'size' => $file->getSize(),
            'name' => $file->getClientOriginalName(),
            'url' => Storage::disk($this->disk)->url($filePath),
            'path' => $filePath,
        ];
    }
}
