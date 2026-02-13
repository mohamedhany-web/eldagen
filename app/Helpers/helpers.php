<?php

if (!function_exists('app_base_path')) {
    /**
     * المسار الأساسي للتطبيق من APP_URL (مثلاً "eldagen" عند http://localhost/eldagen)
     * يستخدم لمقارنة المسارات عند تشغيل المشروع من مجلد فرعي.
     */
    function app_base_path(): string
    {
        $path = parse_url(config('app.url'), PHP_URL_PATH);
        return trim((string) $path, '/');
    }
}

if (!function_exists('request_path_without_base')) {
    /**
     * مسار الطلب بعد إزالة المسار الأساسي للتطبيق (لعمل المشروع من مجلد فرعي)
     * مثلاً: path = "eldagen/maint-admin-secure" و base = "eldagen" → "maint-admin-secure"
     */
    function request_path_without_base(?string $path = null): string
    {
        $path = $path ?? request()->path();
        $base = app_base_path();
        if ($base === '') {
            return trim($path, '/');
        }
        $prefix = $base . '/';
        return str_starts_with($path, $prefix) ? substr($path, strlen($prefix)) : $path;
    }
}

if (!function_exists('storage_url')) {
    /**
     * إنشاء رابط عام لملف في storage/app/public
     * يعمل مع الـ Route المخصص /storage/{path} عند عدم وجود symlink على السيرفر.
     * يستخدم نفس النطاق والبروتوكول الحالي لتفادي مشكلة Mixed Content (http/https).
     */
    function storage_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (app()->has('request') && request()->getHost()) {
            $root = request()->getSchemeAndHttpHost() . request()->getBasePath();
            return rtrim($root, '/') . '/storage/' . $path;
        }

        $base = rtrim(config('app.url'), '/');
        return $base . '/storage/' . $path;
    }
}

if (!function_exists('course_attachment_url')) {
    /**
     * رابط تحميل مرفق درس — يمر عبر مسار واحد مع query لتفادي 404 على الاستضافة
     * (نفس فكرة تمرير الطلب عبر Laravel كما الصور، لكن بدون اعتماد على segments متعددة).
     */
    function course_attachment_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (!str_starts_with($path, 'course-attachments/')) {
            $path = 'course-attachments/' . $path;
        }
        return url()->route('files.course-attachment', ['p' => base64_encode($path)]);
    }
}
