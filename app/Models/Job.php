<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_FULLTIME = 1;
    public const TYPE_PART_TIME = 2;

    public const STATUS_DRAFT = 1;
    public const STATUS_OPEN = 2;
    public const STATUS_CLOSED = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'banner',
        'number_of_recruitment',
        'job_category_id',
        'city_id',
        'address_detail',
        'start_date',
        'end_date',
        'description',
        'request_detail',
        'contact_detail',
        'type',
        'status',
    ];
}
