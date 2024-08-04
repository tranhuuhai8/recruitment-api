<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyContact extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_SHOW = 1;
    const STATUS_HIDE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'contact_type_id',
        'url',
        'status',
    ];
}
