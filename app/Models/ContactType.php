<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    use HasFactory;

    const TYPE_DEFAULT = 1;
    const TYPE_CUSTOMIZE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_name',
        'logo',
        'type'
    ];
}
