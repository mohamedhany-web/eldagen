<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * عمود type في notifications كان يسمح فقط بـ: info, success, warning, error
     * التطبيق يحتاج: general, course, exam, assignment, grade, announcement, remider, warning, system
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // تحديث القيم القديمة لتتوافق مع الـ enum الجديد (info -> general, success -> general, error -> warning)
            DB::table('notifications')
                ->whereIn('type', ['info', 'success'])
                ->update(['type' => 'general']);
            DB::table('notifications')
                ->where('type', 'error')
                ->update(['type' => 'warning']);

            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
                'general', 'course', 'exam', 'assignment', 'grade',
                'announcement', 'reminder', 'warning', 'system'
            ) NOT NULL DEFAULT 'general'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // إرجاع الـ enum للقيم القديمة (مع تحويل القيم الجديدة إلى general أو warning)
            DB::table('notifications')
                ->whereNotIn('type', ['info', 'success', 'warning', 'error'])
                ->update(['type' => 'general']);

            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
                'info', 'success', 'warning', 'error'
            ) NOT NULL DEFAULT 'info'");
        }
    }
};
