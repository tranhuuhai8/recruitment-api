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
    public const STATUS_REVIEWED = 2;
    public const STATUS_ACCEPTED = 3;
    public const STATUS_REJECTED = 4;

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REVIEWED,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'applicant_id',
        'job_id',
        'file_name',
        'file_path',
        'application_file_id',
        'cover_letter',
        'guest_name',
        'guest_email',
        'guest_telephone',
        'status',
    ];
}
