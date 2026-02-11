<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancedCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'academic_subject_id',
        'teacher_id',
        'title',
        'description',
        'objectives',
        'level',
        'duration_hours',
        'price',
        'thumbnail',
        'requirements',
        'what_you_learn',
        'is_active',
        'is_featured',
        'lesson_access_mode',
        'required_watch_percent',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }

    public function activations()
    {
        return $this->hasMany(CourseActivation::class);
    }

    public function exams()
    {
        return $this->hasMany(AdvancedExam::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'advanced_course_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * أكواد التفعيل الخاصة بهذا الكورس
     */
    public function activationCodes()
    {
        return $this->hasMany(CourseActivationCode::class, 'advanced_course_id');
    }

    /**
     * علاقة مع الطلاب المسجلين
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'student_course_enrollments', 'advanced_course_id', 'user_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at']);
    }

    /**
     * علاقة مع الطلاب النشطين فقط
     */
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'student_course_enrollments', 'advanced_course_id', 'user_id')
                    ->wherePivot('status', 'active')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getTotalLessonsAttribute()
    {
        return $this->lessons()->count();
    }

    public function getActivatedStudentsCountAttribute()
    {
        return $this->activations()->where('is_active', true)->count();
    }

    public function isActivatedForUser($userId)
    {
        return $this->activations()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function getProgressForUser($userId)
    {
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) return 0;

        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('course_lesson_id', $this->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    public function getLevelBadgeAttribute()
    {
        $badges = [
            'beginner' => ['text' => 'مبتدئ', 'color' => 'green'],
            'intermediate' => ['text' => 'متوسط', 'color' => 'yellow'],
            'advanced' => ['text' => 'متقدم', 'color' => 'red'],
        ];

        return $badges[$this->level] ?? $badges['beginner'];
    }

    /**
     * هل الكورس بوضع تسلسلي (يجب إكمال الدرس قبل التالي)
     */
    public function isStrictLessonAccess(): bool
    {
        return ($this->lesson_access_mode ?? 'strict') === 'strict';
    }

    /**
     * نسبة المشاهدة المطلوبة لاعتبار الدرس مكتملاً (في الوضع التسلسلي)
     */
    public function getRequiredWatchPercent(): int
    {
        $p = (int) ($this->required_watch_percent ?? 90);
        return max(1, min(100, $p));
    }
}