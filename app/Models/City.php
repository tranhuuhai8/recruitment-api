<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function parent()
    {
        return $this->belongsTo(City::class, 'parent_id');
    }

    /**
     * Method child
     *
     * @return HasMany
     */
    public function child(): HasMany
    {
        return $this->hasMany(City::class, 'parent_id');
    }

    /**
     * Method jobs
     *
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'city_id');
    }

    /**
     * Method companies
     *
     * @return HasMany
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'city_id');
    }
}
