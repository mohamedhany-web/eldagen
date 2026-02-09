<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountViolation extends Model
{
    protected $fillable = [
        'user_id',
        'violation_type',
        'notes',
        'ip_address',
        'reinstated_at',
        'reinstated_by',
    ];

    protected $casts = [
        'reinstated_at' => 'datetime',
    ];

    public const TYPE_SCREENSHOT = 'screenshot';
    public const TYPE_RECORDING = 'recording';
    public const TYPE_OTHER = 'other';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reinstatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reinstated_by');
    }

    public function getViolationTypeLabelAttribute(): string
    {
        return match ($this->violation_type) {
            self::TYPE_SCREENSHOT => 'محاولة تصوير شاشة / سكرين شوت',
            self::TYPE_RECORDING => 'محاولة تسجيل الشاشة (سكرين ريكورد)',
            default => $this->violation_type,
        };
    }
}
