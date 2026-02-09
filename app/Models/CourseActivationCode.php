<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseActivationCode extends Model
{
    protected $table = 'course_activation_codes';

    protected $fillable = [
        'code',
        'advanced_course_id',
        'created_by',
        'used_at',
        'used_by',
        'order_id',
        'status',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeUsed($query)
    {
        return $query->where('status', self::STATUS_USED);
    }

    public function isUsed(): bool
    {
        return $this->status === self::STATUS_USED;
    }

    /**
     * توليد كود فريد (أحرف وأرقام كبيرة)
     */
    public static function generateUniqueCode(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789'; // بدون 0,O,1,I,L
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
