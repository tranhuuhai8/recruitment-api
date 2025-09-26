<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class City extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'status',
        'parent_id',
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
        return $this->belongsTo(City::class, 'parent_id');
    }
}
