<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'parent_phone',
        'password',
        'role',
        'parent_id',
        'is_active',
        'profile_image',
        'birth_date',
        'address',
        'academic_year_id',
        'last_login_at',
        'suspended_at',
        'suspension_reason',
        'preferences',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'suspended_at' => 'datetime',
            'preferences' => 'array',
            'permissions' => 'array',
        ];
    }

    /**
     * مخالفات الحساب (تصوير شاشة، تسجيل، إلخ)
     */
    public function accountViolations()
    {
        return $this->hasMany(AccountViolation::class);
    }

    /**
     * هل الحساب موقوف؟
     */
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /**
     * علاقة مع الجهاز المسموح للطالب (جهاز واحد فقط)
     */
    public function studentDevice()
    {
        return $this->hasOne(StudentDevice::class);
    }

    /**
     * علاقة مع ولي الأمر
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * علاقة مع الأطفال (للوالدين)
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * علاقة مع السنة الدراسية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * علاقة مع تسجيلات الكورسات
     */
    public function courseEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'user_id');
    }

    /**
     * علاقة مع الفصول (للطالب عبر classroom_students)
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_students', 'student_id', 'classroom_id')
            ->withPivot('enrolled_at', 'is_active')
            ->withTimestamps();
    }

    /**
     * علاقة مع محاولات الامتحان
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * علاقة مع التقارير كطالب
     */
    public function studentReports()
    {
        return $this->hasMany(StudentReport::class, 'student_id');
    }

    /**
     * علاقة مع التقارير كولي أمر
     */
    public function parentReports()
    {
        return $this->hasMany(StudentReport::class, 'parent_id');
    }

    /**
     * علاقة مع رسائل الواتساب
     */
    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * علاقة مع الإشعارات المخصصة (تجاوز Laravel's built-in)
     */
    public function customNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * تجاوز علاقة notifications الافتراضية
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * التحقق من كون المستخدم طالب
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * التحقق من كون المستخدم مدرس
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * التحقق من كون المستخدم إداري
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * التحقق من صلاحية المستخدم على مفتاح معين (للإداريين فقط).
     * إذا كانت صلاحياته فارغة = جميع الصلاحيات؛ وإلا يُتحقق من وجود المفتاح.
     */
    public function hasPermission(string $key): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }
        $permissions = $this->permissions ?? [];
        if (empty($permissions)) {
            return true; // صلاحيات كاملة
        }
        // دعم prefix: admin.users.edit -> نتحقق من admin.users
        if (in_array($key, $permissions, true)) {
            return true;
        }
        $prefix = explode('.', $key);
        array_pop($prefix);
        while (!empty($prefix)) {
            if (in_array(implode('.', $prefix), $permissions, true)) {
                return true;
            }
            array_pop($prefix);
        }
        return false;
    }

    /**
     * التحقق من كون المستخدم ولي أمر
     */
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    /**
     * scope للطلاب
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * scope للمدرسين
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    /**
     * scope لأولياء الأمور
     */
    public function scopeParents($query)
    {
        return $query->where('role', 'parent');
    }

    /**
     * scope للمستخدمين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على الكورسات النشطة للطالب
     */
    public function activeCourses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'student_course_enrollments', 'user_id', 'advanced_course_id')
                    ->where('student_course_enrollments.status', 'active');
    }

    /**
     * التحقق من التسجيل في كورس
     */
    public function isEnrolledIn($courseId): bool
    {
        return $this->courseEnrollments()
                    ->where('advanced_course_id', $courseId)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * الحصول على تسجيل الكورس
     */
    public function getCourseEnrollment($courseId)
    {
        return $this->courseEnrollments()
                    ->where('advanced_course_id', $courseId)
                    ->first();
    }

    /**
     * الحصول على آخر تقرير شهري
     */
    public function getLastMonthlyReport()
    {
        return $this->studentReports()
                    ->where('report_type', 'monthly')
                    ->latest()
                    ->first();
    }

    /**
     * الحصول على متوسط الدرجات
     */
    public function getAverageScore()
    {
        return $this->examAttempts()
                    ->where('status', 'completed')
                    ->avg('percentage') ?? 0;
    }

    /**
     * الحصول على عدد الامتحانات المكتملة
     */
    public function getCompletedExamsCount()
    {
        return $this->examAttempts()
                    ->where('status', 'completed')
                    ->count();
    }

    /**
     * تحديث آخر دخول
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}