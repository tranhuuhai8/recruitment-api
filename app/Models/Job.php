<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    public const TYPE_FULLTIME = 1;
    public const TYPE_PART_TIME = 2;

    public const JOB_TYPES = [
        self::TYPE_FULLTIME,
        self::TYPE_PART_TIME,
    ];

    public const STATUS_DRAFT = 1;
    public const STATUS_OPEN = 2;
    public const STATUS_CLOSED = 3;

    public const JOB_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];

    public const JOB_NOTIFY = [0, 1, 7];

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
        'description',
        'notify_frequency',
        'type',
        'status',
        'last_sent_notify',
    ];

    protected $casts = [
        'last_sent_notify' => 'datetime',
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
     * Method applications
     *
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Method favorites
     *
     * @return HasMany
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(JobFavorite::class);
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
