<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // التحقق من وجود الجدول قبل التعديل (جدول الدروس: lessons)
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (!Schema::hasColumn('lessons', 'video_url')) {
                    $table->string('video_url')->nullable()->after('content');
                }
                if (!Schema::hasColumn('lessons', 'attachments')) {
                    $table->json('attachments')->nullable()->after('video_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (Schema::hasColumn('lessons', 'attachments')) {
                    $table->dropColumn('attachments');
                }
            });
        }
    }
};
