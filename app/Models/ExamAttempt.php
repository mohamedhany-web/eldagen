<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'time_taken',
        'score',
        'percentage',
        'status',
        'answers',
        'ip_address',
        'user_agent',
        'tab_switches',
        'suspicious_activities',
        'auto_submitted',
        'reviewed_by',
        'reviewed_at',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'answers' => 'array',
        'suspicious_activities' => 'array',
        'auto_submitted' => 'boolean',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'time_taken' => 'integer', // بالثواني
        'tab_switches' => 'integer',
    ];

    /**
     * علاقة مع الامتحان
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * علاقة مع الطالب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع المراجع
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * scope للمحاولات المكتملة (مسلّمة أو تسليم تلقائي)
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['submitted', 'auto_submitted']);
    }

    /**
     * scope للمحاولات الجارية
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * التحقق من انتهاء الوقت
     * المدة مخزنة بالدقائق، والحساب بالثواني
     * إذا المدة 0 أو غير محددة لا يُعتبر الامتحان منتهياً (تجنب فتح صفحة النتيجة فوراً)
     */
    public function isTimeExpired()
    {
        if (!$this->started_at || $this->status !== 'in_progress') {
            return false;
        }

        $durationMinutes = $this->exam->duration_minutes ?? 0;
        if ($durationMinutes <= 0) {
            return false;
        }

        $timeLimitSeconds = (int) $durationMinutes * 60;
        $elapsed = (int) now()->diffInSeconds($this->started_at, true);

        return $elapsed >= $timeLimitSeconds;
    }

    /**
     * الحصول على الوقت المتبقي بالثواني (للعرض والعداد في الواجهة)
     * إذا المدة 0 أو غير محددة يُعاد ساعة واحدة لتفادي تسليم فوري
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->started_at || $this->status !== 'in_progress') {
            return 0;
        }

        $durationMinutes = $this->exam->duration_minutes ?? 0;
        if ($durationMinutes <= 0) {
            return 3600; // ساعة افتراضية حتى لا يُسلّم الامتحان فوراً
        }

        $timeLimitSeconds = (int) $durationMinutes * 60;
        $elapsed = (int) now()->diffInSeconds($this->started_at, true);

        return max(0, $timeLimitSeconds - $elapsed);
    }

    /**
     * الحصول على الوقت المستغرق منسق
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->time_taken) {
            return 'غير محدد';
        }

        $hours = floor($this->time_taken / 3600);
        $minutes = floor(($this->time_taken % 3600) / 60);
        $seconds = $this->time_taken % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * الحصول على حالة النتيجة
     * passing_marks في الامتحان مخزنة كنسبة مئوية (0-100)
     */
    public function getResultStatusAttribute()
    {
        if ($this->status === 'in_progress') {
            return 'غير مكتمل';
        }

        $percentage = $this->percentage ?? 0;
        if ((float) $percentage >= (float) $this->exam->passing_marks) {
            return 'ناجح';
        }

        return 'راسب';
    }

    /**
     * الحصول على لون حالة النتيجة
     */
    public function getResultColorAttribute()
    {
        if ($this->status === 'in_progress') {
            return 'gray';
        }

        $percentage = $this->percentage ?? 0;
        if ((float) $percentage >= (float) $this->exam->passing_marks) {
            return 'green';
        }

        return 'red';
    }

    /**
     * حساب النتيجة
     */
    public function calculateScore()
    {
        $totalScore = 0;
        $totalMarks = 0;

        foreach ($this->exam->examQuestions as $examQuestion) {
            $question = $examQuestion->question;
            $userAnswer = $this->answers[$question->id] ?? null;
            $totalMarks += $examQuestion->marks;

            if ($question->isCorrectAnswer($userAnswer)) {
                $totalScore += $examQuestion->marks;
            }
        }

        $this->update([
            'score' => $totalScore,
            'percentage' => $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0,
        ]);

        return $totalScore;
    }

    /**
     * تسجيل نشاط مشبوه
     */
    public function logSuspiciousActivity($activity, $details = null)
    {
        $activities = $this->suspicious_activities ?? [];
        $activities[] = [
            'activity' => $activity,
            'details' => $details,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
        ];

        $this->update(['suspicious_activities' => $activities]);
    }

    /**
     * زيادة عداد تبديل التبويبات
     */
    public function incrementTabSwitches()
    {
        $this->increment('tab_switches');
        $this->logSuspiciousActivity('tab_switch', 'تم تبديل التبويب أو النافذة');
    }

    /**
     * التحقق من وجود أنشطة مشبوهة
     */
    public function hasSuspiciousActivities()
    {
        return !empty($this->suspicious_activities) || $this->tab_switches > 0;
    }
}