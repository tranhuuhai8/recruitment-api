<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
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
        'contact_name',
        'logo',
        'type',
        'status',
    ];
}
