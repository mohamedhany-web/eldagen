<?php

namespace App\Observers;

use App\Models\Exam;
use App\Models\ActivityLog;

class ExamObserver
{
    /**
     * Handle the Exam "created" event.
     */
    public function created(Exam $exam): void
    {
        ActivityLog::logActivity(
            'exam_created',
            $exam,
            null,
            $exam->only(['title', 'advanced_course_id', 'duration_minutes', 'passing_marks'])
        );
    }

    /**
     * Handle the Exam "updated" event.
     */
    public function updated(Exam $exam): void
    {
        $changes = $exam->getChanges();
        
        if (!empty($changes)) {
            // تحديد نوع التحديث المحدد
            $action = 'exam_updated';
            if (isset($changes['is_active'])) {
                $action = 'exam_status_changed';
            } elseif (isset($changes['is_published'])) {
                $action = 'exam_published_status_changed';
            }

            ActivityLog::logActivity(
                $action,
                $exam,
                $exam->getOriginal(),
                $changes
            );
        }
    }

    /**
     * Handle the Exam "deleted" event.
     */
    public function deleted(Exam $exam): void
    {
        ActivityLog::logActivity(
            'exam_deleted',
            $exam,
            $exam->only(['title', 'advanced_course_id', 'duration_minutes']),
            null
        );
    }

    /**
     * Handle the Exam "restored" event.
     */
    public function restored(Exam $exam): void
    {
        ActivityLog::logActivity(
            'exam_restored',
            $exam,
            null,
            $exam->only(['title', 'advanced_course_id'])
        );
    }
}