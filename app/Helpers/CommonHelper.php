<?php

namespace App\Helpers;

class CommonHelper
{
    /**
     * Method makeDataJobApplication
     *
     * @param $data [explicite description]
     *
     * @return array
     */
    public static function makeDataJobApplication($data): array
    {
        $newData = [];
        foreach ($data as $item) {
            $newData[] = [
                'name' => $item->applicant?->name ?? $item->guest_name,
                'email' => $item->applicant?->user?->mail_address ?? $item->guest_email,
                'telephone' => $item->applicant?->telephone ?? $item->guest_telephone,
                'file_name' => $item->applicationFile?->file_name ?? $item->file_name,
                'file_path' => $item->applicationFile?->file_path ?? $item->file_path,
                'created_at' => $item->created_at->format('d/m/Y H:i'),
            ];
        }

        return $newData;
    }
}
