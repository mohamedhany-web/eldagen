<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // الثقة بالـ proxy (مثلاً nginx) لضمان التعرف على HTTPS والنطاق الصحيح — يساعد في ظهور روابط الصور على السيرفر
        $middleware->trustProxies(at: '*');
        // التحقق من وضع الصيانة أولاً، ثم مراقبة الأنشطة، ثم التحقق من تعليق الحساب
        $middleware->web([
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\LogActivityMiddleware::class,
            \App\Http\Middleware\CheckSuspendedAccount::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
