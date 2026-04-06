<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLog extends Model
{
    use HasFactory;

    // ── Status Constants ──────────────────────────────
    public const STATUS_PENDING = 1;
    public const STATUS_SENT    = 2;
    public const STATUS_FAILED  = 3;
    public const STATUS_BOUNCED = 4;

    public const LOG_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_SENT,
        self::STATUS_FAILED,
        self::STATUS_BOUNCED,
    ];

    // ── Fillable ──────────────────────────────────────
    protected $fillable = [
        'contact_id',
        'mail_template_id',
        'from_email',
        'to_email',
        'to_name',
        'subject',
        'body',
        'status',
        'sent_at',
        'failed_reason',
        'metadata',
    ];

    // ── Casts ─────────────────────────────────────────
    protected $casts = [
        'status'   => 'integer',
        'sent_at'  => 'datetime',
        'metadata' => 'array',
    ];

    // ── Relationships ─────────────────────────────────
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function mailTemplate(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }
}
