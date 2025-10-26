<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationFile extends Model
{
    use HasFactory;

    public const TAKE_RECENTLY_UPLOADED = 10;

    protected $table = 'application_file';

    protected $fillable = [
        'applicant_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
