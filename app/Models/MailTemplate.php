<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ── Type Constants ────────────────────────────────
    public const TYPE_CONFIRMATION = 1;
    public const TYPE_REPLY        = 2;
    public const TYPE_NOTIFICATION = 3;

    public const TEMPLATE_TYPES = [
        self::TYPE_CONFIRMATION,
        self::TYPE_REPLY,
        self::TYPE_NOTIFICATION,
    ];

    // ── System Codes ──────────────────────────────────
    public const CODE_CONTACT_CONFIRMATION = 'contact_confirmation';
    public const CODE_CONTACT_REPLY        = 'contact_reply';

    // ── Fillable ──────────────────────────────────────
    protected $fillable = [
        'name',
        'code',
        'subject',
        'body',
        'variables',
        'type',
        'is_active',
    ];

    // ── Casts ─────────────────────────────────────────
    protected $casts = [
        'type'      => 'integer',
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    // ── Relationships ─────────────────────────────────
    public function mailLogs(): HasMany
    {
        return $this->hasMany(MailLog::class);
    }
}
