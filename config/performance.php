<?php

return [

    /*
    |--------------------------------------------------------------------------
    | أوقات التخزين المؤقت (بالدقائق)
    |--------------------------------------------------------------------------
    */

    'cache_ttl' => [
        'home_page' => (int) env('CACHE_TTL_HOME', 10),
        'admin_dashboard' => (int) env('CACHE_TTL_ADMIN_DASHBOARD', 2),
        'admin_dashboard_stats' => (int) env('CACHE_TTL_ADMIN_STATS', 5),
        'catalog_index' => (int) env('CACHE_TTL_CATALOG', 5),
        'catalog_show' => (int) env('CACHE_TTL_COURSE_PAGE', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | أحجام الصفحات (للتحجيم مع عدد كبير من الطلاب)
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'users' => (int) env('PAGINATION_USERS', 20),
        'courses' => (int) env('PAGINATION_COURSES', 15),
        'exams' => (int) env('PAGINATION_EXAMS', 15),
        'orders' => (int) env('PAGINATION_ORDERS', 20),
        'notifications' => (int) env('PAGINATION_NOTIFICATIONS', 20),
        'activity_log' => (int) env('PAGINATION_ACTIVITY_LOG', 25),
        'questions' => (int) env('PAGINATION_QUESTIONS', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | الإشعارات الجماعية (Queue للتوسع)
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'queue_bulk_threshold' => (int) env('NOTIFICATION_QUEUE_BULK_THRESHOLD', 50),
        'queue_name' => env('NOTIFICATION_QUEUE_NAME', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | رؤوس التخزين المؤقت للمتصفح (بالثواني)
    |--------------------------------------------------------------------------
    */
    'browser_cache_seconds' => [
        'home' => 300,
        'catalog' => 300,
        'static_pages' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | توصيات الإنتاج
    |--------------------------------------------------------------------------
    | CACHE_STORE=redis, SESSION_DRIVER=redis, QUEUE_CONNECTION=redis
    | php artisan config:cache && php artisan route:cache && php artisan view:cache
    */

];
