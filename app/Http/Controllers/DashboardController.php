<?php

namespace App\Http\Controllers;

use App\Contracts\DashboardStatsContract;
use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardStatsContract $dashboardStats
    ) {}

    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'teacher':
                return $this->teacherDashboard();
            case 'student':
                return $this->studentDashboard();
            case 'parent':
                return $this->parentDashboard();
            default:
                return redirect('/');
        }
    }

    /**
     * لوحة تحكم الإدارة (SOLID: تعتمد على العقد، إحصائيات مع cache)
     */
    private function adminDashboard()
    {
        $stats = $this->dashboardStats->getAdminStats();
        $recent_users = User::latest()->take(5)->get(['id', 'name', 'email', 'phone', 'role', 'created_at']);
        $recent_courses = Course::with('teacher:id,name', 'subject:id,name')->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recent_users', 'recent_courses'));
    }

    private function teacherDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'my_courses' => $user->taughtCourses()->count(),
            'total_students' => $user->taughtCourses()->withCount('students')->get()->sum('students_count'),
            'my_classrooms' => Classroom::where('teacher_id', $user->id)->count(),
        ];

        $my_courses = $user->taughtCourses()->with('subject', 'students')->latest()->take(5)->get();
        $my_classrooms = Classroom::where('teacher_id', $user->id)->with('students')->latest()->take(5)->get();

        return view('dashboard.teacher', compact('stats', 'my_courses', 'my_classrooms'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();
        
        // الكورسات المفعلة للطالب
        $activeCourses = $user->activeCourses()
            ->with(['academicYear', 'academicSubject', 'teacher'])
            ->get();

        // الطلبات الأخيرة
        $recentOrders = \App\Models\Order::where('user_id', $user->id)
            ->with(['course.academicYear', 'course.academicSubject'])
            ->latest()
            ->take(5)
            ->get();

        // الاختبارات القادمة (كورسات الطالب المفعلة)
        $courseIds = $user->activeCourses()->pluck('advanced_course_id');
        $upcomingExams = \App\Models\Exam::whereIn('advanced_course_id', $courseIds)
            ->where('is_published', true)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })
            ->with('course:id,title')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // إحصائيات
        $stats = [
            'active_courses' => $activeCourses->count(),
            'pending_orders' => \App\Models\Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_courses' => $user->courseEnrollments()->where('status', 'completed')->count(),
            'total_progress' => $this->calculateOverallProgress($user),
            'passed_exams' => \App\Models\ExamAttempt::where('user_id', $user->id)->whereIn('exam_attempts.status', ['submitted', 'auto_submitted'])->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')->whereColumn('exam_attempts.percentage', '>=', 'exams.passing_marks')->count(),
        ];

        return view('dashboard.student', compact('stats', 'activeCourses', 'recentOrders', 'upcomingExams'));
    }

    private function calculateOverallProgress($user)
    {
        $enrollments = $user->courseEnrollments()->where('status', 'active')->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->sum('progress');
        return round($totalProgress / $enrollments->count(), 1);
    }

    private function parentDashboard()
    {
        $user = Auth::user();

        $children = $user->children()
            ->withCount(['courseEnrollments', 'classrooms'])
            ->with(['activeCourses' => fn ($q) => $q->with('academicSubject:id,name')])
            ->get();
        $stats = [
            'total_children' => $children->count(),
            'total_courses' => $children->sum('course_enrollments_count'),
            'total_classrooms' => $children->sum('classrooms_count'),
        ];

        return view('dashboard.parent', compact('stats', 'children'));
    }
}
