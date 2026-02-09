<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLoginActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        ActivityLog::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'action' => 'login',
            'description' => "تسجيل دخول المستخدم: {$user->name} ({$user->role})",
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'old_values' => null,
            'new_values' => [
                'login_time' => now()->toDateTimeString(),
                'user_role' => $user->role,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => 'POST',
            'response_code' => 200,
            'duration' => null,
        ]);
    }
}