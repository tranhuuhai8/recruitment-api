<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_FULLTIME = 1;
    public const TYPE_PART_TIME = 2;

    public const SALARY_TYPE_NET = 1;
    public const SALARY_TYPE_GROSS = 2;
    public const SALARY_TYPE_EXCHANGE = 3;

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
        'title',
        'banner',
        'number_of_recruitment',
        'job_category_id',
        'city_id',
        'start_date',
        'end_date',
        'salary_min',
        'salary_max',
        'salary_type',
        'description',
        'type',
        'status',
    ];

    /**
     * Method company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Method city
     *
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Method jobCategory
     *
     * @return BelongsTo
     */
    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }
}
