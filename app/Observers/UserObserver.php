<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // تجنب تسجيل النشاط إذا لم يكن هناك مستخدم مسجل دخول
        if (auth()->check()) {
            ActivityLog::logActivity(
                'user_created',
                $user,
                null,
                $user->only(['name', 'email', 'phone', 'role'])
            );
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // تجنب تسجيل النشاط إذا لم يكن هناك مستخدم مسجل دخول
        if (!auth()->check()) {
            return;
        }

        $changes = $user->getChanges();
        
        // إزالة البيانات الحساسة من التسجيل
        if (isset($changes['password'])) {
            unset($changes['password']);
        }
        if (isset($changes['remember_token'])) {
            unset($changes['remember_token']);
        }

        if (!empty($changes)) {
            ActivityLog::logActivity(
                'user_updated',
                $user,
                $user->getOriginal(),
                $changes
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (auth()->check()) {
            ActivityLog::logActivity(
                'user_deleted',
                $user,
                $user->only(['name', 'email', 'phone', 'role']),
                null
            );
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        ActivityLog::logActivity(
            'user_restored',
            $user,
            null,
            $user->only(['name', 'email', 'phone', 'role'])
        );
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        ActivityLog::logActivity(
            'user_force_deleted',
            $user,
            $user->only(['name', 'email', 'phone', 'role']),
            null
        );
    }
}