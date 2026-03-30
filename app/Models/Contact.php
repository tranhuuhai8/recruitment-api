<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ── Status Constants ──────────────────────────────
    public const STATUS_NEW         = 1;
    public const STATUS_READ        = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_RESOLVED    = 4;
    public const STATUS_SPAM        = 5;

    public const CONTACT_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_READ,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED,
        self::STATUS_SPAM,
    ];

    // ── Priority Constants ────────────────────────────
    public const PRIORITY_LOW    = 1;
    public const PRIORITY_NORMAL = 2;
    public const PRIORITY_HIGH   = 3;
    public const PRIORITY_URGENT = 4;

    public const CONTACT_PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    // ── Fillable ──────────────────────────────────────
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'title',
        'content',
        'status',
        'priority',
        'admin_note',
        'replied_at',
        'ip_address',
    ];

    // ── Casts ─────────────────────────────────────────
    protected $casts = [
        'status'      => 'integer',
        'priority'    => 'integer',
        'replied_at'  => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────
    public function mailLogs(): HasMany
    {
        return $this->hasMany(MailLog::class);
    }
}
