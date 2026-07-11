<?php

namespace App\Http\Resources\Admin\ApplicationFile;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'applicant_id' => $this->applicant_id,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'created_at' => DateHelper::parseDateBe($this->created_at),
        ];
    }
}
