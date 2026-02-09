<?php

namespace App\Services;

use App\Contracts\DashboardStatsContract;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\ActivityLog;
use App\Models\VideoWatch;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * خدمة إحصائيات لوحة التحكم (SOLID: Single Responsibility, Open/Closed)
 * مسؤولية واحدة: تجميع وإرجاع إحصائيات لوحة التحكم مع cache لدعم التوسع
 */
class DashboardStatsService implements DashboardStatsContract
{
    public function __construct(
        protected int $cacheTtlMinutes
    ) {}

    /**
     * إحصائيات لوحة تحكم الإدارة مع تخزين مؤقت
     */
    public function getAdminStats(): array
    {
        $key = 'admin_dashboard_stats';
        $ttl = now()->addMinutes($this->cacheTtlMinutes);

        return Cache::remember($key, $ttl, function () {
            return [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_teachers' => User::where('role', 'teacher')->count(),
                'total_parents' => User::where('role', 'parent')->count(),
                'total_courses' => Course::count(),
                'published_courses' => Course::where('status', 'published')->count(),
                'total_subjects' => Subject::count(),
                'total_exams' => Exam::count(),
                'total_question_banks' => QuestionBank::count(),
                'active_students' => User::where('role', 'student')
                    ->where('is_active', true)
                    ->whereHas('courseEnrollments')
                    ->count(),
                'recent_activities' => ActivityLog::with('user:id,name')
                    ->latest()
                    ->take(10)
                    ->get(),
                'recent_exam_attempts' => ExamAttempt::with(['exam:id,title', 'user:id,name,email'])
                    ->where('status', 'submitted')
                    ->latest()
                    ->take(10)
                    ->get(),
                'video_watch_stats' => VideoWatch::selectRaw('COUNT(*) as total_watches, AVG(progress_percentage) as avg_progress')
                    ->first(),
            ];
        });
    }

    /**
     * إحصائيات شهرية للإدارة (بدون cache طويل لتعكس البيانات الحالية)
     */
    public function getAdminMonthlyStats(): array
    {
        $year = now()->year;
        $month = now()->month;

        return [
            'new_users_this_month' => User::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'exams_this_month' => Exam::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'course_enrollments_this_month' => DB::table('student_course_enrollments')
                ->whereYear('enrolled_at', $year)
                ->whereMonth('enrolled_at', $month)
                ->count(),
        ];
    }

    /**
     * إبطال cache الإحصائيات
     */
    public function forgetAdminStatsCache(): void
    {
        Cache::forget('admin_dashboard_stats');
    }
}
