<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Route تقديم الملفات من storage/app/public (حل مشكلة عدم ظهور الصور أونلاين)
|--------------------------------------------------------------------------
| يجب أن يكون هذا الـ Route في بداية الملف لتفادي تداخل Laravel مع storage.local.
| config/filesystems.php: local disk يجب أن يكون فيه 'serve' => false.
| لا تضف في .htaccess قواعد تسمح بالوصول المباشر لـ /storage/ وتتخطى index.php.
*/
Route::get('/storage/{path}', function ($path) {
    try {
        $path = rawurldecode($path);
        $path = str_replace('..', '', $path);
        $path = ltrim($path, '/');

        $basePath = storage_path('app/public');
        $basePath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $basePath), DIRECTORY_SEPARATOR);

        if (!@is_dir($basePath)) {
            \Log::warning('Storage base path does not exist', ['base_path' => $basePath]);
            abort(404, 'File not found');
        }

        $pathNormalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $filePath = $basePath . DIRECTORY_SEPARATOR . $pathNormalized;

        if (!@file_exists($filePath)) {
            \Log::warning('Storage file not found', ['requested_path' => $path, 'file_path' => $filePath, 'base_path' => $basePath]);
            abort(404, 'File not found');
        }

        if (!@is_file($filePath)) {
            \Log::warning('Storage path is not a file', ['requested_path' => $path, 'file_path' => $filePath]);
            abort(404, 'Not a file');
        }

        // التحقق من أن الملف داخل basePath (بدون الاعتماد على realpath لأنه قد يفشل على استضافة مشتركة)
        $filePathReal = @realpath($filePath) ?: $filePath;
        $basePathReal = @realpath($basePath) ?: $basePath;
        $sep = DIRECTORY_SEPARATOR;
        $baseNorm = rtrim(str_replace(['/', '\\'], $sep, $basePathReal), $sep);
        $fileNorm = str_replace(['/', '\\'], $sep, $filePathReal);
        if ($baseNorm !== $fileNorm && strpos($fileNorm, $baseNorm . $sep) !== 0) {
            \Log::warning('Storage access denied - path outside allowed directory', [
                'requested_path' => $path,
                'file_path' => $filePath,
                'base_path' => $basePath,
            ]);
            abort(404, 'Access denied');
        }

        if (!@is_readable($filePathReal)) {
            \Log::warning('Storage file not readable', ['requested_path' => $path, 'file_path' => $filePathReal]);
            abort(403, 'File not readable');
        }

        $mimeType = @mime_content_type($filePathReal);
        if (!$mimeType) {
            $extension = strtolower(pathinfo($filePathReal, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ];
        if ($mimeType === 'application/pdf') {
            $headers['Content-Disposition'] = 'inline; filename="' . basename($filePathReal) . '"';
        }

        return response()->file($filePathReal, $headers);
    } catch (\Exception $e) {
        \Log::error('Storage route error', [
            'path' => $path ?? 'unknown',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        abort(404, 'File not found');
    }
})->where('path', '.*')->name('storage.file')->middleware('web');

// SEO: خريطة الموقع وملف robots
Route::get('/sitemap.xml', function () {
    $base = rtrim(config('app.url'), '/');
    $urls = [
        ['loc' => $base . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => $base . '/catalog', 'changefreq' => 'daily', 'priority' => '0.9'],
        ['loc' => $base . '/login', 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => $base . '/register', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ];
    $courses = \App\Models\AdvancedCourse::where('is_active', true)->pluck('id');
    foreach ($courses as $id) {
        $urls[] = ['loc' => $base . '/catalog/' . $id, 'changefreq' => 'weekly', 'priority' => '0.8'];
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $u) {
        $xml .= '<url><loc>' . htmlspecialchars($u['loc']) . '</loc><changefreq>' . ($u['changefreq'] ?? 'weekly') . '</changefreq><priority>' . ($u['priority'] ?? '0.5') . '</priority></url>';
    }
    $xml .= '</urlset>';
    return response($xml, 200, ['Content-Type' => 'application/xml', 'Cache-Control' => 'public, max-age=3600']);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $sitemap = rtrim(config('app.url'), '/') . '/sitemap.xml';
    $txt = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /dashboard\nDisallow: /my-courses\nDisallow: /exams\nDisallow: /profile\nDisallow: /settings\nDisallow: /notifications\nDisallow: /api/\nSitemap: {$sitemap}\n";
    return response($txt, 200, ['Content-Type' => 'text/plain', 'Cache-Control' => 'public, max-age=86400']);
})->name('robots');

// الصفحة الرئيسية (مع تخزين مؤقت 10 دقائق لتقليل الضغط على السيرفر)
Route::get('/', function () {
    $academicYears = Cache::remember('home_academic_years', now()->addMinutes(config('performance.cache_ttl.home_page', 10)), function () {
        return \App\Models\AcademicYear::where('is_active', true)
            ->withCount('academicSubjects')
            ->orderBy('order')
            ->get();
    });

    return response()
        ->view('welcome', compact('academicYears'))
        ->header('Cache-Control', 'public, max-age=300'); // 5 دقائق في المتصفح
})->name('home');

// كتالوج الكورسات (خارج المنصة - بدون تسجيل دخول)
Route::get('/catalog', [\App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{advancedCourse}', [\App\Http\Controllers\CatalogController::class, 'show'])->name('catalog.show');

// الرابط الثابت للأدمن أثناء الصيانة: من يمتلكه فقط يمكنه فتح صفحة تسجيل الدخول (بدون توكن)
// المسار يُسجّل كاملًا ليعمل من مجلد فرعي (مثلاً /eldagen/maint-admin-secure)
$bypassPath = trim(config('maintenance.bypass_path', 'maint-admin-secure'), '/');
$bypassUri = app_base_path() ? (app_base_path() . '/' . $bypassPath) : $bypassPath;
Route::get($bypassUri, function (\Illuminate\Http\Request $request) {
    $effectivePath = trim(request_path_without_base($request->path()), '/');
    $expectedPath = trim(config('maintenance.bypass_path', 'maint-admin-secure'), '/');
    if ($effectivePath !== $expectedPath) {
        abort(404);
    }
    if ($request->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return app(AuthController::class)->showLogin($request);
})->name('maintenance.admin-entry');

// مسارات المصادقة (مع تحديد معدل الطلبات للحماية من الاختراقات)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// مسارات لوحة التحكم
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // مسارات الطلاب
    Route::get('/academic-years', [\App\Http\Controllers\Student\AcademicYearController::class, 'index'])->name('academic-years');
    Route::get('/academic-years/{academicYear}/subjects', [\App\Http\Controllers\Student\AcademicYearController::class, 'subjects'])->name('academic-years.subjects');
    Route::get('/subjects/{academicSubject}/courses', [\App\Http\Controllers\Student\SubjectController::class, 'courses'])->name('subjects.courses');
    Route::get('/courses/{advancedCourse}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
    
    // صفحة الحساب الموقوف (للطلاب المخالفين)
    Route::get('/account-suspended', function () {
        if (!auth()->user()->isSuspended()) {
            return redirect()->route('dashboard');
        }
        return view('student.account-suspended');
    })->name('account.suspended');

    // كورساتي المفعلة
    Route::get('/my-courses', [\App\Http\Controllers\Student\MyCourseController::class, 'index'])->name('my-courses.index');
    Route::get('/my-courses/{course}', [\App\Http\Controllers\Student\MyCourseController::class, 'show'])->name('my-courses.show');
    Route::post('/my-courses/report-violation', [\App\Http\Controllers\Student\MyCourseController::class, 'reportViolation'])->name('my-courses.report-violation');
    Route::get('/my-courses/{course}/lessons/{lesson}/watch', [\App\Http\Controllers\Student\MyCourseController::class, 'watchLesson'])
        ->middleware(\App\Http\Middleware\VideoProtectionMiddleware::class)
        ->name('my-courses.lesson.watch');
    Route::post('/my-courses/{course}/lessons/{lesson}/progress', [\App\Http\Controllers\Student\MyCourseController::class, 'updateLessonProgress'])->name('my-courses.lesson.progress');
    Route::post('/my-courses/{course}/lessons/{lesson}/report-duration', [\App\Http\Controllers\Student\MyCourseController::class, 'reportLessonDuration'])->name('my-courses.lesson.report-duration');
    Route::post('/my-courses/{course}/lessons/{lesson}/video-question-answer', [\App\Http\Controllers\Student\MyCourseController::class, 'checkVideoQuestionAnswer'])->name('my-courses.lesson.video-question-answer');
    
    // API للدروس
    Route::get('/api/lessons/{lesson}', function(\App\Models\CourseLesson $lesson) {
        // التحقق من أن المستخدم مسجل في الكورس
        $user = auth()->user();
        if (!$user->isEnrolledIn($lesson->advanced_course_id)) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'content' => $lesson->content,
            'type' => $lesson->type,
            'video_url' => $lesson->video_url,
            'video_source' => $lesson->video_source,
            'duration_minutes' => $lesson->duration_minutes,
            'attachments' => $lesson->attachments ? json_decode($lesson->attachments, true) : null
        ]);
    });

    // نظام الطلبات
    Route::post('/courses/{advancedCourse}/order', [\App\Http\Controllers\Student\OrderController::class, 'store'])->name('courses.order');
    Route::get('/orders', [\App\Http\Controllers\Student\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Student\OrderController::class, 'show'])->name('orders.show');
    
    // امتحانات الطلاب
    Route::prefix('exams')->name('student.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{exam}/attempts/{attempt}/take', [\App\Http\Controllers\Student\ExamController::class, 'take'])
            ->middleware(\App\Http\Middleware\VideoProtectionMiddleware::class)
            ->name('take');
        Route::post('/{exam}/attempts/{attempt}/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::post('/{exam}/attempts/{attempt}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
        Route::get('/{exam}/attempts/{attempt}/result', [\App\Http\Controllers\Student\ExamController::class, 'result'])->name('result');
        Route::post('/{exam}/attempts/{attempt}/tab-switch', [\App\Http\Controllers\Student\ExamController::class, 'logTabSwitch'])->name('tab-switch');
    });

    // صفحات الطلاب
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'update'])->name('settings.update');
    Route::get('/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/cleanup', [\App\Http\Controllers\Student\NotificationController::class, 'cleanup'])->name('notifications.cleanup');
    Route::get('/api/notifications/unread-count', [\App\Http\Controllers\Student\NotificationController::class, 'getUnreadCount'])->middleware('throttle:api-notifications')->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [\App\Http\Controllers\Student\NotificationController::class, 'getRecent'])->middleware('throttle:api-notifications')->name('notifications.recent');
    Route::get('/api/notifications/unread-for-popup', [\App\Http\Controllers\Student\NotificationController::class, 'getUnreadForPopup'])->middleware('throttle:api-notifications')->name('notifications.unread-for-popup');
    Route::get('/calendar', [\App\Http\Controllers\Student\CalendarController::class, 'index'])->name('calendar');
    
    // مسارات الإدارة — للأسمن فقط + التحقق من الصلاحية المخصصة
    Route::prefix('admin')->name('admin.')->middleware([
        \App\Http\Middleware\EnsureUserIsAdmin::class,
        \App\Http\Middleware\CheckAdminPermission::class,
    ])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        // وضع الصيانة
        Route::get('/maintenance', [\App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/enable', [\App\Http\Controllers\Admin\MaintenanceController::class, 'enable'])->name('maintenance.enable');
        Route::post('/maintenance/disable', [\App\Http\Controllers\Admin\MaintenanceController::class, 'disable'])->name('maintenance.disable');

        // إدارة الطلاب المخالفين (الحسابات الموقوفة)
        Route::get('/suspended-students', [\App\Http\Controllers\Admin\SuspendedStudentsController::class, 'index'])->name('suspended-students.index');
        Route::post('/suspended-students/{user}/reinstate', [\App\Http\Controllers\Admin\SuspendedStudentsController::class, 'reinstate'])->name('suspended-students.reinstate');

        // الصلاحيات — التحكم في دور كل مستخدم وظهوره في السايدبار
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionsController::class, 'index'])->name('permissions.index');
        Route::put('/permissions/{user}', [\App\Http\Controllers\Admin\PermissionsController::class, 'update'])->name('permissions.update');

        // إدارة المستخدمين
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete');

        // التحكم بتسجيلات دخول الطلاب (جهاز واحد لكل طالب)
        Route::get('/student-sessions', [\App\Http\Controllers\Admin\StudentSessionsController::class, 'index'])->name('student-sessions.index');
        Route::delete('/student-sessions/revoke/{user}', [\App\Http\Controllers\Admin\StudentSessionsController::class, 'revoke'])->name('student-sessions.revoke');
        
        // إدارة السنوات الدراسية
        Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
        Route::post('/academic-years/{academicYear}/toggle-status', [\App\Http\Controllers\Admin\AcademicYearController::class, 'toggleStatus'])->name('academic-years.toggle-status');
        Route::post('/academic-years/reorder', [\App\Http\Controllers\Admin\AcademicYearController::class, 'reorder'])->name('academic-years.reorder');

        // إدارة المواد الدراسية
        Route::resource('academic-subjects', \App\Http\Controllers\Admin\AcademicSubjectController::class);
        Route::post('/academic-subjects/{academicSubject}/toggle-status', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'toggleStatus'])->name('academic-subjects.toggle-status');
        Route::post('/academic-subjects/reorder', [\App\Http\Controllers\Admin\AcademicSubjectController::class, 'reorder'])->name('academic-subjects.reorder');

        // إدارة الكورسات المتطورة
        Route::resource('advanced-courses', \App\Http\Controllers\Admin\AdvancedCourseController::class);
        Route::post('/advanced-courses/{advancedCourse}/activate-student', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'activateStudent'])->name('advanced-courses.activate-student');
        Route::get('/advanced-courses/{advancedCourse}/students', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'students'])->name('advanced-courses.students');
        Route::post('/advanced-courses/{advancedCourse}/toggle-status', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleStatus'])->name('advanced-courses.toggle-status');
        Route::post('/advanced-courses/{advancedCourse}/toggle-featured', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'toggleFeatured'])->name('advanced-courses.toggle-featured');
        Route::get('/advanced-courses/{advancedCourse}/orders', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'orders'])->name('advanced-courses.orders');
        Route::get('/advanced-courses/{advancedCourse}/statistics', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'statistics'])->name('advanced-courses.statistics');
        Route::get('/advanced-courses/{advancedCourse}/lesson-answers', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'lessonAnswers'])->name('advanced-courses.lesson-answers');
        Route::post('/advanced-courses/{advancedCourse}/duplicate', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'duplicate'])->name('advanced-courses.duplicate');
        Route::get('/get-subjects-by-year', [\App\Http\Controllers\Admin\AdvancedCourseController::class, 'getSubjectsByYear'])->name('advanced-courses.get-subjects-by-year');
        Route::get('/courses/{course}/lessons-list', function(\App\Models\AdvancedCourse $course) {
            return response()->json($course->lessons()->active()->select('id', 'title')->get());
        });

        // أقسام الكورس (لتنظيم المحاضرات)
        Route::prefix('courses/{course}/sections')->name('courses.sections.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CourseSectionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CourseSectionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CourseSectionController::class, 'store'])->name('store');
            Route::get('/{section}/edit', [\App\Http\Controllers\Admin\CourseSectionController::class, 'edit'])->name('edit');
            Route::put('/{section}', [\App\Http\Controllers\Admin\CourseSectionController::class, 'update'])->name('update');
            Route::delete('/{section}', [\App\Http\Controllers\Admin\CourseSectionController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\CourseSectionController::class, 'reorder'])->name('reorder');
        });

        // إدارة دروس الكورسات
        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CourseLessonController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CourseLessonController::class, 'store'])->name('store');
            Route::get('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'show'])->name('show');
            Route::get('/{lesson}/edit', [\App\Http\Controllers\Admin\CourseLessonController::class, 'edit'])->name('edit');
            Route::put('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [\App\Http\Controllers\Admin\CourseLessonController::class, 'destroy'])->name('destroy');
            Route::post('/{lesson}/toggle-status', [\App\Http\Controllers\Admin\CourseLessonController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/reorder', [\App\Http\Controllers\Admin\CourseLessonController::class, 'reorder'])->name('reorder');
            // أسئلة الفيديو (عند دقيقة معينة)
            Route::get('/{lesson}/video-questions', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'index'])->name('video-questions.index');
            Route::post('/{lesson}/video-questions', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'store'])->name('video-questions.store');
            Route::delete('/{lesson}/video-questions/{videoQuestion}', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'destroy'])->name('video-questions.destroy');
        });
        // بنك الأسئلة للاختيار (للأسئلة داخل الفيديو)
        Route::get('/lesson-video-questions/bank-categories', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'bankCategories'])->name('lesson-video-questions.bank-categories');
        Route::get('/lesson-video-questions/bank-questions', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'bankQuestions'])->name('lesson-video-questions.bank-questions');
        Route::get('/lesson-video-questions/question-preview/{question}', [\App\Http\Controllers\Admin\LessonVideoQuestionController::class, 'questionPreview'])->name('lesson-video-questions.question-preview');

        // إدارة بنك الأسئلة
        Route::prefix('question-bank')->name('question-bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionBankController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionBankController::class, 'store'])->name('store');
            Route::get('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'show'])->name('show');
            Route::get('/{question}/edit', [\App\Http\Controllers\Admin\QuestionBankController::class, 'edit'])->name('edit');
            Route::put('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'update'])->name('update');
            Route::delete('/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroy'])->name('destroy');
            Route::post('/{question}/duplicate', [\App\Http\Controllers\Admin\QuestionBankController::class, 'duplicate'])->name('duplicate');
            Route::post('/export', [\App\Http\Controllers\Admin\QuestionBankController::class, 'export'])->name('export');
            Route::post('/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'import'])->name('import');
        });

        // إدارة تصنيفات الأسئلة
        Route::prefix('question-categories')->name('question-categories.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'store'])->name('store');
            Route::get('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'show'])->name('show');
            Route::get('/{questionCategory}/edit', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'edit'])->name('edit');
            Route::put('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'update'])->name('update');
            Route::delete('/{questionCategory}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'reorder'])->name('reorder');
            Route::get('/subjects-by-year/{year}', [\App\Http\Controllers\Admin\QuestionCategoryController::class, 'getSubjectsByYear'])->name('subjects-by-year');
        });

        // إدارة الامتحانات
        Route::prefix('exams')->name('exams.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ExamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExamController::class, 'store'])->name('store');
            Route::get('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'show'])->name('show');
            Route::get('/{exam}/edit', [\App\Http\Controllers\Admin\ExamController::class, 'edit'])->name('edit');
            Route::put('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'update'])->name('update');
            Route::delete('/{exam}', [\App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('destroy');
            Route::get('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'manageQuestions'])->name('questions.manage');
            Route::post('/{exam}/questions', [\App\Http\Controllers\Admin\ExamController::class, 'addQuestion'])->name('questions.add');
            Route::post('/{exam}/questions/bulk', [\App\Http\Controllers\Admin\ExamController::class, 'addQuestionsBulk'])->name('questions.bulk');
            Route::delete('/{exam}/questions/{examQuestion}', [\App\Http\Controllers\Admin\ExamController::class, 'removeQuestion'])->name('questions.remove');
            Route::post('/{exam}/questions/reorder', [\App\Http\Controllers\Admin\ExamController::class, 'reorderQuestions'])->name('questions.reorder');
            Route::post('/{exam}/toggle-publish', [\App\Http\Controllers\Admin\ExamController::class, 'togglePublish'])->name('toggle-publish');
            Route::post('/{exam}/toggle-status', [\App\Http\Controllers\Admin\ExamController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{exam}/grant-attempt/{user}', [\App\Http\Controllers\Admin\ExamController::class, 'grantAttempt'])->name('grant-attempt');
            Route::get('/{exam}/statistics', [\App\Http\Controllers\Admin\ExamController::class, 'statistics'])->name('statistics');
            Route::get('/{exam}/preview', [\App\Http\Controllers\Admin\ExamController::class, 'preview'])->name('preview');
            Route::post('/{exam}/duplicate', [\App\Http\Controllers\Admin\ExamController::class, 'duplicate'])->name('duplicate');
            Route::get('/{exam}/attempts/export', [\App\Http\Controllers\Admin\ExamController::class, 'exportAttempts'])->name('attempts.export');
        });

        // إدارة المواد الدراسية القديمة
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);

        // إدارة الكورسات القديمة
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

        // سجل النشاطات
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-log.show');

        // الإحصائيات
        Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics');
        Route::get('/statistics/users', [\App\Http\Controllers\Admin\StatisticsController::class, 'users'])->name('statistics.users');
        Route::get('/statistics/courses', [\App\Http\Controllers\Admin\StatisticsController::class, 'courses'])->name('statistics.courses');

        // أكواد التفعيل
        Route::get('/activation-codes', [\App\Http\Controllers\Admin\ActivationCodeController::class, 'index'])->name('activation-codes.index');
        Route::post('/activation-codes', [\App\Http\Controllers\Admin\ActivationCodeController::class, 'store'])->name('activation-codes.store');
        Route::get('/activation-codes/export', [\App\Http\Controllers\Admin\ActivationCodeController::class, 'export'])->name('activation-codes.export');

        // إدارة الطلبات
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])->name('orders.reject');

        // إدارة الإشعارات
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
            Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/quick-send', [\App\Http\Controllers\Admin\NotificationController::class, 'quickSend'])->name('quick-send');
            Route::get('/target-count', [\App\Http\Controllers\Admin\NotificationController::class, 'getTargetCount'])->name('target-count');
            Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::post('/cleanup', [\App\Http\Controllers\Admin\NotificationController::class, 'cleanup'])->name('cleanup');
            Route::get('/statistics', [\App\Http\Controllers\Admin\NotificationController::class, 'statistics'])->name('statistics');
        });

        // إدارة تسجيل الطلاب في الكورسات
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'store'])->name('store');
            Route::get('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'show'])->name('show');
            Route::post('/{enrollment}/activate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'activate'])->name('activate');
            Route::post('/{enrollment}/deactivate', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'deactivate'])->name('deactivate');
            Route::post('/{enrollment}/update-progress', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateProgress'])->name('update-progress');
            Route::post('/{enrollment}/update-notes', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'updateNotes'])->name('update-notes');
            Route::delete('/{enrollment}', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'destroy'])->name('destroy');
            Route::get('/search/by-phone', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'searchStudentByPhone'])->name('search-by-phone');
        });

        // التقارير (طلاب وكل بيانات كل طالب)
        Route::get('messages', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('messages.index');
        Route::get('messages/student/{user}', [\App\Http\Controllers\Admin\ReportsController::class, 'student'])->name('messages.student');
        Route::get('messages/student/{user}/excel', [\App\Http\Controllers\Admin\ReportsController::class, 'exportStudentExcel'])->name('messages.student.excel');

    });
});