<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * إضافة رؤوس أمان معروفة للحماية من الاختراقات الشائعة
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // منع عرض الصفحة داخل iframe (حماية من clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // منع المتصفح من تخمين نوع المحتوى (MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // تفعيل فلتر XSS في المتصفح (للإصدارات القديمة)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // التحكم في إرسال Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // تقييد صلاحيات المتصفح (ميكروفون، كاميرا، إلخ)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // سياسة المحتوى - أساسية (تعدّل حسب احتياجك لتجنب كسر السكربتات الخارجية)
        // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self'");

        // إجبار HTTPS في الإنتاج
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
