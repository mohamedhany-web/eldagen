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
        if (!Schema::hasTable('exams')) {
            return;
        }

        if (!Schema::hasColumn('exams', 'course_lesson_id') && Schema::hasTable('course_lessons')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->unsignedBigInteger('course_lesson_id')->nullable()->after('advanced_course_id');
                $table->foreign('course_lesson_id')->references('id')->on('course_lessons')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('exams') || !Schema::hasColumn('exams', 'course_lesson_id')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['course_lesson_id']);
            $table->dropColumn('course_lesson_id');
        });
    }
};
