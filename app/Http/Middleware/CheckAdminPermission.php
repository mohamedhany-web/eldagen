<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * التحقق من صلاحية الإداري على المسار الحالي (من اسم المسار).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (!$routeName || !str_starts_with($routeName, 'admin.')) {
            return $next($request);
        }

        $parts = explode('.', $routeName);
        $routeMap = config('permissions.route_to_permission', []);
        $prefixes = config('permissions.route_prefixes', []);
        $key = null;
        for ($i = count($parts); $i >= 2; $i--) {
            $candidate = implode('.', array_slice($parts, 0, $i));
            if (isset($routeMap[$candidate])) {
                $key = $routeMap[$candidate];
                break;
            }
            if (in_array($candidate, $prefixes, true)) {
                $key = $candidate;
                break;
            }
        }
        if (!$key) {
            $key = $parts[0] . '.' . ($parts[1] ?? '');
        }

        if ($user->hasPermission($key)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'ليس لديك صلاحية الدخول إلى هذه الصفحة.'], 403);
        }
        return redirect()->route('admin.dashboard')->with('error', 'ليس لديك صلاحية الدخول إلى هذه الصفحة.');
    }
}
