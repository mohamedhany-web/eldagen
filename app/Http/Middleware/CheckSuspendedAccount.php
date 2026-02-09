<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        if ($request->user()->isSuspended()) {
            // السماح بعرض صفحة "حسابك موقوف" وبتسجيل الخروج فقط
            if ($request->routeIs('account.suspended') || $request->routeIs('logout')) {
                return $next($request);
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'account_suspended',
                    'message' => 'تم تعليق حسابك بسبب مخالفة قواعد الاستخدام (تصوير شاشة أو تسجيل). تواصل مع الإدارة لإعادة تفعيل الحساب.',
                ], 403);
            }
            return redirect()->route('account.suspended');
        }

        return $next($request);
    }
}
