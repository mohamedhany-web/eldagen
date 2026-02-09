<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة فهارس لتحسين أداء الاستعلامات الشائعة
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
            });
        }

        if (Schema::hasTable('exam_attempts')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                if (Schema::hasColumn('exam_attempts', 'user_id')) {
                    $table->index(['exam_id', 'user_id']);
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role']);
            });
        }

        if (Schema::hasTable('exam_attempts')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                if (Schema::hasColumn('exam_attempts', 'user_id')) {
                    $table->dropIndex(['exam_id', 'user_id']);
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'created_at']);
            });
        }
    }
};
