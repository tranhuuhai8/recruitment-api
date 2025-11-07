<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TAKE_RECENTLY_UPLOADED = 10;

    protected $table = 'application_file';

    protected $fillable = [
        'applicant_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'order',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
