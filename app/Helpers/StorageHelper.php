<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * deleteFileByPath
     *
     * @param  string $filePath
     * @return void
     */
    public static function deleteFileByPath(string $filePath): void
    {
        $bucket = config('filesystems.aws_bucket');
        $region = config('filesystems.aws_region');
        $prefix = "https://{$bucket}.s3.{$region}.amazonaws.com/";

        if (str_contains($filePath, $bucket)) {
            $keyFile = str_replace($prefix, '', $filePath);
            Storage::disk('s3')->delete($keyFile);
            Log::info("Remove file " . $filePath . " successfully");
        }
    }
}
