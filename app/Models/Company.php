<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'city_id',
        'logo', 'cover_img',
        'name', 'short_name',
        'telephone', 'website',
        'address', 'description'
    ];

    /**
     * The column the slug is derived from.
     */
    protected function slugSourceColumn(): string
    {
        return 'name';
    }

    /**
     * Method user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Method jobs
     *
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'company_id');
    }
}
