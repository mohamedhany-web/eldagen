<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'advanced_course_id',
        'enrolled_at',
        'activated_at',
        'activated_by',
        'status',
        'progress',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'activated_at' => 'datetime',
        'progress' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة مع الكورس
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع المستخدم الذي فعل التسجيل
     */
    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    /**
     * تحديد ما إذا كان التسجيل نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * تحديد ما إذا كان التسجيل مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * الحصول على لون حالة التسجيل
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'active' => 'green',
            'completed' => 'blue',
            'suspended' => 'red',
            default => 'gray'
        };
    }

    /**
     * الحصول على نص حالة التسجيل
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'suspended' => 'معلق',
            default => 'غير معروف'
        };
    }
}
