<?php

namespace App\Providers;

use App\Contracts\DashboardStatsContract;
use App\Contracts\NotificationDispatcherInterface;
use App\Services\DashboardStatsService;
use App\Services\NotificationDispatcherService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services (SOLID: Dependency Inversion - ربط العقد بالتنفيذ).
     */
    public function register(): void
    {
        $this->app->singleton(DashboardStatsContract::class, function ($app) {
            $ttl = config('performance.cache_ttl.admin_dashboard_stats', 5);
            return new DashboardStatsService($ttl);
        });

        $this->app->singleton(NotificationDispatcherInterface::class, function ($app) {
            $threshold = config('performance.notifications.queue_bulk_threshold', 50);
            $queueName = config('performance.notifications.queue_name', 'default');
            return new NotificationDispatcherService($threshold, $queueName);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تحديد معدل الطلبات للحماية من هجمات القوة الغاشمة
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip() . ':' . ($request->input('email') ?? $request->ip()));
        });
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
        RateLimiter::for('api-notifications', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // تسجيل Model Observers للمراقبة الشاملة
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Exam::observe(\App\Observers\ExamObserver::class);
        \App\Models\AdvancedCourse::observe(\App\Observers\AdvancedCourseObserver::class);
        \App\Models\ExamAttempt::observe(\App\Observers\ExamAttemptObserver::class);

        // تسجيل Event Listeners للمراقبة الشاملة
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogLoginActivity::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Listeners\LogLogoutActivity::class
        );

        // تسجيل المحاولات الفاشلة لتسجيل الدخول
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            function (\Illuminate\Auth\Events\Failed $event) {
                \App\Models\ActivityLog::create([
                    'user_id' => null,
                    'session_id' => session()->getId(),
                    'action' => 'failed_login',
                    'description' => "محاولة دخول فاشلة للإيميل: " . ($event->credentials['email'] ?? 'غير معروف'),
                    'model_type' => null,
                    'model_id' => null,
                    'old_values' => null,
                    'new_values' => [
                        'attempted_email' => $event->credentials['email'] ?? null,
                        'failed_at' => now()->toDateTimeString(),
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->fullUrl(),
                    'method' => 'POST',
                    'response_code' => 401,
                    'duration' => null,
                ]);
            }
        );
    }
}
