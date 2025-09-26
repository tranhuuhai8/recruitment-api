<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class JobCategory extends Model
{
    use HasFactory;

    public const TYPE_DEFAULT = 1;
    public const TYPE_CUSTOMIZE = 2;

    public const STATUS_SHOW = 1;
    public const STATUS_HIDE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'parent_id'
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::flush();
        });

        static::updated(function () {
            Cache::flush();
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

    public function parent()
    {
        return $this->belongsTo(JobCategory::class, 'parent_id');
    }
}
