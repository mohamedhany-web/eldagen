<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLogoutActivity
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
    public function handle(Logout $event): void
    {
        $user = $event->user;
        
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'session_id' => session()->getId(),
                'action' => 'logout',
                'description' => "تسجيل خروج المستخدم: {$user->name} ({$user->role})",
                'model_type' => get_class($user),
                'model_id' => $user->id,
                'old_values' => null,
                'new_values' => [
                    'logout_time' => now()->toDateTimeString(),
                    'user_role' => $user->role,
                    'user_name' => $user->name,
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
}