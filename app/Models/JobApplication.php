<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PENDING = 1;
    public const STATUS_ACCEPTED = 2;
    public const STATUS_REJECTED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'job_id',
        'file',
        'title',
        'description',
        'status',
    ];
}
