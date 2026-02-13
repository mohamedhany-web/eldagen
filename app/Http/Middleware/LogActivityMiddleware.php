<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    /**
     * مراقبة جميع الطلبات وتسجيل الأنشطة
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // تسجيل الأنشطة فقط للمستخدمين المسجلين
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * تسجيل النشاط حسب نوع الطلب
     */
    private function logActivity(Request $request, Response $response)
    {
        $user = Auth::user();
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        $route = $request->route();
        
        // تجاهل الطلبات التي لا تحتاج تسجيل
        if ($this->shouldIgnore($path, $method)) {
            return;
        }

        $action = $this->determineAction($method, $path, $route);
        $description = $this->getActionDescription($action, $path, $request);

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $this->getModelType($path),
            'model_id' => $this->getModelId($route),
            'old_values' => null,
            'new_values' => $method === 'POST' || $method === 'PUT' || $method === 'PATCH' 
                ? $this->sanitizeData($request->except(['_token', '_method', 'password', 'password_confirmation'])) 
                : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $method,
            'response_code' => $response->getStatusCode(),
        ]);
    }

    /**
     * تحديد ما إذا كان يجب تجاهل هذا الطلب
     */
    private function shouldIgnore(string $path, string $method): bool
    {
        $ignorePaths = [
            '/api/notifications',
            '/api/user/status',
            '/_debugbar',
            '/horizon',
            '/telescope',
        ];

        $ignorePatterns = [
            '/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i',
            '/\/api\/.*\/status$/',
            '/\/livewire\//',
        ];

        // تجاهل المسارات المحددة
        foreach ($ignorePaths as $ignorePath) {
            if (str_starts_with($path, $ignorePath)) {
                return true;
            }
        }

        // تجاهل الأنماط المحددة
        foreach ($ignorePatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        // تجاهل طلبات GET للـ assets
        if ($method === 'GET' && (
            str_contains($path, '/css/') ||
            str_contains($path, '/js/') ||
            str_contains($path, '/images/')
        )) {
            return true;
        }

        // تقليل الضغط: عدم تسجيل كل زيارة صفحة (dashboard، الصفحة الرئيسية) لتخفيف الكتابة على DB
        if ($method === 'GET' && (
            $path === '/' ||
            $path === '/dashboard' ||
            str_starts_with($path, '/dashboard') ||
            preg_match('#^/admin/dashboard#', $path)
        )) {
            return true;
        }

        return false;
    }

    /**
     * تحديد نوع النشاط
     */
    private function determineAction(string $method, string $path, $route): string
    {
        $routeName = $route ? $route->getName() : '';

        // أنشطة محددة حسب الـ route
        if ($routeName) {
            if (str_contains($routeName, 'exams')) {
                if (str_contains($routeName, 'create') || str_contains($routeName, 'store')) return 'exam_created';
                if (str_contains($routeName, 'edit') || str_contains($routeName, 'update')) return 'exam_updated';
                if (str_contains($routeName, 'destroy')) return 'exam_deleted';
                if (str_contains($routeName, 'show')) return 'exam_viewed';
                if (str_contains($routeName, 'questions')) return 'exam_questions_managed';
                if (str_contains($routeName, 'statistics')) return 'exam_statistics_viewed';
                if (str_contains($routeName, 'preview')) return 'exam_previewed';
                if (str_contains($routeName, 'take')) return 'exam_taken';
                if (str_contains($routeName, 'result')) return 'exam_result_viewed';
            }

            if (str_contains($routeName, 'courses')) {
                if (str_contains($routeName, 'create') || str_contains($routeName, 'store')) return 'course_created';
                if (str_contains($routeName, 'edit') || str_contains($routeName, 'update')) return 'course_updated';
                if (str_contains($routeName, 'destroy')) return 'course_deleted';
                if (str_contains($routeName, 'show')) return 'course_viewed';
                if (str_contains($routeName, 'lessons')) return 'lesson_activity';
            }

            if (str_contains($routeName, 'users')) {
                if (str_contains($routeName, 'create') || str_contains($routeName, 'store')) return 'user_created';
                if (str_contains($routeName, 'edit') || str_contains($routeName, 'update')) return 'user_updated';
                if (str_contains($routeName, 'destroy')) return 'user_deleted';
                if (str_contains($routeName, 'show')) return 'user_profile_viewed';
            }

            if (str_contains($routeName, 'question')) {
                if (str_contains($routeName, 'create') || str_contains($routeName, 'store')) return 'question_created';
                if (str_contains($routeName, 'edit') || str_contains($routeName, 'update')) return 'question_updated';
                if (str_contains($routeName, 'destroy')) return 'question_deleted';
            }
        }

        // أنشطة عامة حسب HTTP method
        return match($method) {
            'GET' => 'page_visited',
            'POST' => 'data_created',
            'PUT', 'PATCH' => 'data_updated',
            'DELETE' => 'data_deleted',
            default => 'unknown_action'
        };
    }

    /**
     * الحصول على وصف النشاط
     */
    private function getActionDescription(string $action, string $path, Request $request): string
    {
        $descriptions = [
            'exam_created' => 'إنشاء امتحان جديد',
            'exam_updated' => 'تحديث امتحان',
            'exam_deleted' => 'حذف امتحان',
            'exam_viewed' => 'عرض تفاصيل امتحان',
            'exam_questions_managed' => 'إدارة أسئلة امتحان',
            'exam_statistics_viewed' => 'عرض إحصائيات امتحان',
            'exam_previewed' => 'معاينة امتحان',
            'exam_taken' => 'بدء أداء امتحان',
            'exam_result_viewed' => 'عرض نتائج امتحان',
            'course_created' => 'إنشاء كورس جديد',
            'course_updated' => 'تحديث كورس',
            'course_deleted' => 'حذف كورس',
            'course_viewed' => 'عرض تفاصيل كورس',
            'lesson_activity' => 'نشاط في الدروس',
            'user_created' => 'إنشاء مستخدم جديد',
            'user_updated' => 'تحديث بيانات مستخدم',
            'user_deleted' => 'حذف مستخدم',
            'user_profile_viewed' => 'عرض ملف مستخدم',
            'question_created' => 'إنشاء سؤال جديد',
            'question_updated' => 'تحديث سؤال',
            'question_deleted' => 'حذف سؤال',
            'page_visited' => 'زيارة صفحة',
            'data_created' => 'إنشاء بيانات',
            'data_updated' => 'تحديث بيانات',
            'data_deleted' => 'حذف بيانات',
        ];

        $baseDescription = $descriptions[$action] ?? 'نشاط غير معروف';
        
        // إضافة تفاصيل أكثر
        if (str_contains($path, '/admin/')) {
            $baseDescription = '[لوحة الإدارة] ' . $baseDescription;
        } elseif (str_contains($path, '/student/')) {
            $baseDescription = '[لوحة الطالب] ' . $baseDescription;
        }

        return $baseDescription;
    }

    /**
     * الحصول على نوع النموذج من المسار
     */
    private function getModelType(string $path): ?string
    {
        if (str_contains($path, '/exams/')) return 'App\\Models\\Exam';
        if (str_contains($path, '/courses/')) return 'App\\Models\\AdvancedCourse';
        if (str_contains($path, '/users/')) return 'App\\Models\\User';
        if (str_contains($path, '/questions/')) return 'App\\Models\\Question';
        if (str_contains($path, '/academic-years/')) return 'App\\Models\\AcademicYear';
        if (str_contains($path, '/academic-subjects/')) return 'App\\Models\\AcademicSubject';
        if (str_contains($path, '/enrollments/')) return 'App\\Models\\StudentCourseEnrollment';
        
        return null;
    }

    /**
     * الحصول على معرف النموذج من الـ route
     */
    private function getModelId($route): ?int
    {
        if (!$route) return null;

        $parameters = $route->parameters();
        
        // البحث عن معرف النموذج في parameters
        foreach ($parameters as $param) {
            if (is_object($param) && method_exists($param, 'getKey')) {
                return $param->getKey();
            } elseif (is_numeric($param)) {
                return (int) $param;
            }
        }

        return null;
    }

    /**
     * تنظيف البيانات الحساسة
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[محذوف للأمان]';
            }
        }

        return $data;
    }
}