<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * فهارس إضافية لدعم التوسع مع عدد كبير من الطلاب
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'is_active']);
            });
        }

        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                if (Schema::hasColumn('student_course_enrollments', 'advanced_course_id') && Schema::hasColumn('student_course_enrollments', 'status')) {
                    $table->index(['advanced_course_id', 'status']);
                }
                if (Schema::hasColumn('student_course_enrollments', 'user_id')) {
                    $table->index('user_id');
                }
            });
        }

        if (Schema::hasTable('exam_attempts') && Schema::hasColumn('exam_attempts', 'user_id') && Schema::hasColumn('exam_attempts', 'status')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role', 'is_active']);
            });
        }
        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                if (Schema::hasColumn('student_course_enrollments', 'advanced_course_id') && Schema::hasColumn('student_course_enrollments', 'status')) {
                    $table->dropIndex(['advanced_course_id', 'status']);
                }
                if (Schema::hasColumn('student_course_enrollments', 'user_id')) {
                    $table->dropIndex(['user_id']);
                }
            });
        }
        if (Schema::hasTable('exam_attempts')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'status']);
            });
        }
    }
};
