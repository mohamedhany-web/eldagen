<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * عند تفعيل الصيانة: فقط لوحة الأدمن تعمل، باقي الموقع يعرض صفحة الصيانة.
     * الأدمن يدخل عبر الرابط الثابت (المسار السري) ثم يسجل الدخول.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Cache::get('maintenance_mode', false)) {
            return $next($request);
        }

        $path = trim(\request_path_without_base($request->path()), '/');
        $bypassPath = trim(config('maintenance.bypass_path', 'maint-admin-secure'), '/');

        // الرابط الثابت للأدمن: من يمتلك هذا المسار فقط يمكنه فتح صفحة تسجيل الدخول
        if ($path === $bypassPath) {
            return $next($request);
        }

        // السماح بمسارات تسجيل الدخول والخروج
        $fullPath = trim($request->path(), '/');
        if ($request->routeIs('login') || $request->routeIs('logout') ||
            $path === 'login' || $path === 'logout' ||
            str_starts_with($path, 'login') || str_starts_with($path, 'logout') ||
            str_contains($fullPath, 'login') || str_contains($fullPath, 'logout')) {
            return $next($request);
        }

        // التحقق من المستخدم المسجل
        $user = $request->user();
        
        // إذا كان المستخدم مسجلاً وهو أدمن، اسمح له بالوصول لجميع المسارات
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // السماح لجميع مسارات الأدمن حتى لو لم يكن مسجلاً
        if (str_starts_with($path, 'admin') || str_contains($fullPath, 'admin')) {
            return $next($request);
        }

        // أي طلب آخر (غير مسجل، أو ليس أدمن، أو ليس مسار أدمن) → صفحة الصيانة
        return response()->view('maintenance', [], 503);
    }
}
