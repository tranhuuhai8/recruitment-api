<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSalary extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_NET = 1;
    const TYPE_GROSS = 2;
    const TYPE_EXCHANGE = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'type',
        'salary_min',
        'salary_max',
    ];
}
