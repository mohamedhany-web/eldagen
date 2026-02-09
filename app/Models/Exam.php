<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'advanced_course_id',
        'course_lesson_id',
        'title',
        'description',
        'instructions',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'attempts_allowed',
        'randomize_questions',
        'randomize_options',
        'show_results_immediately',
        'show_correct_answers',
        'show_explanations',
        'allow_review',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_active',
        'is_published',
        'require_camera',
        'require_microphone',
        'prevent_tab_switch',
        'auto_submit',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_explanations' => 'boolean',
        'allow_review' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'require_camera' => 'boolean',
        'require_microphone' => 'boolean',
        'prevent_tab_switch' => 'boolean',
        'auto_submit' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'settings' => 'array',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
    ];

    /**
     * علاقة مع الكورس
     */
    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع الدرس
     */
    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    /**
     * علاقة مع منشئ الامتحان
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع أسئلة الامتحان
     */
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    /**
     * علاقة مع الأسئلة
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot(['order', 'marks', 'time_limit'])
                    ->orderBy('exam_questions.order');
    }

    /**
     * علاقة مع محاولات الامتحان
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * علاقة مع منح المحاولات الإضافية (السماح بمحاولة أخرى للطالب)
     */
    public function extraAttemptGrants()
    {
        return $this->hasMany(ExamExtraAttemptGrant::class);
    }

    /**
     * عدد المحاولات الإضافية الممنوحة لطالب معين
     */
    public function getExtraAttemptsGrantedCount($userId)
    {
        return $this->extraAttemptGrants()->where('user_id', $userId)->count();
    }

    /**
     * scope للامتحانات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * scope للامتحانات المنشورة
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * scope للامتحانات المتاحة حالياً
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('start_time')
                          ->orWhere('start_time', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('end_time')
                          ->orWhere('end_time', '>=', now());
                    });
    }

    /**
     * التحقق من إتاحة الامتحان
     */
    public function isAvailable()
    {
        if (!$this->is_active || !$this->is_published) {
            return false;
        }

        if ($this->start_time && $this->start_time->isFuture()) {
            return false;
        }

        if ($this->end_time && $this->end_time->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * التحقق من إمكانية المحاولة للطالب (مع احتساب المحاولات الإضافية الممنوحة)
     */
    public function canAttempt($userId)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $attemptCount = $this->attempts()
                            ->where('user_id', $userId)
                            ->count();

        $effectiveAllowed = $this->attempts_allowed === 0
            ? PHP_INT_MAX
            : $this->attempts_allowed + $this->getExtraAttemptsGrantedCount($userId);

        return $this->attempts_allowed === 0 || $attemptCount < $effectiveAllowed;
    }

    /**
     * الحصول على عدد المحاولات المتبقية للطالب (مع المحاولات الإضافية الممنوحة)
     */
    public function getRemainingAttempts($userId)
    {
        if ($this->attempts_allowed === 0) {
            return 'غير محدود';
        }

        $attemptCount = $this->attempts()
                            ->where('user_id', $userId)
                            ->count();

        $effectiveAllowed = $this->attempts_allowed + $this->getExtraAttemptsGrantedCount($userId);

        return max(0, $effectiveAllowed - $attemptCount);
    }

    /**
     * الحصول على أفضل نتيجة للطالب
     */
    public function getBestScore($userId)
    {
        return $this->attempts()
                   ->where('user_id', $userId)
                   ->whereIn('status', ['submitted', 'auto_submitted'])
                   ->max('score');
    }

    /**
     * الحصول على آخر محاولة للطالب
     */
    public function getLastAttempt($userId)
    {
        return $this->attempts()
                   ->where('user_id', $userId)
                   ->latest()
                   ->first();
    }

    /**
     * حساب إجمالي الدرجات
     */
    public function calculateTotalMarks()
    {
        return $this->examQuestions()->sum('marks');
    }

    /**
     * الحصول على إحصائيات الامتحان
     */
    public function getStatsAttribute()
    {
        $attempts = $this->attempts()->whereIn('status', ['submitted', 'auto_submitted']);
        $totalAttempts = $attempts->count();
        
        // درجة النجاح مخزنة كنسبة مئوية (0-100)
        $passedAttempts = $totalAttempts > 0 ? $attempts->where('percentage', '>=', $this->passing_marks)->count() : 0;
        $passRate = $totalAttempts > 0 ? ($passedAttempts / $totalAttempts) * 100 : 0;
        
        return [
            'total_attempts' => $totalAttempts,
            'average_score' => $attempts->avg('score') ?? 0,
            'highest_score' => $attempts->max('score') ?? 0,
            'lowest_score' => $attempts->min('score') ?? 0,
            'pass_rate' => round($passRate, 2),
            'passed_attempts' => $passedAttempts,
            'failed_attempts' => $totalAttempts - $passedAttempts,
        ];
    }
}