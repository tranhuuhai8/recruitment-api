<?php

namespace App\Services\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Method uploadImg
     *
     * @param Request $request [explicite description]
     * @param string $disk [explicite description]
     *
     * @return array
     */
    public function uploadImg(Request $request, $disk = 'public'): array
    {
        $file = $request->file('file');
        $filePath = $file->store('attachments', $disk);

        return [
            'url' => Storage::disk($disk)->url($filePath),
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
    public function uploadPdf(Request $request, $disk = 'public'): array
    {
        $file = $request->file('file');
        $filePath = $file->store('pdfs', $disk);

        return [
            'size' => $file->getSize(),
            'name' => $file->getClientOriginalName(),
            'url' => Storage::disk($disk)->url($filePath),
            'path' => $filePath,
        ];
    }
}
