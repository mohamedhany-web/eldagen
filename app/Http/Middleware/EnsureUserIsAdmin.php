<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * منع غير الأدمن من الوصول إلى أي مسار يبدأ بـ admin/
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالوصول.'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'ليس لديك صلاحية الدخول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
