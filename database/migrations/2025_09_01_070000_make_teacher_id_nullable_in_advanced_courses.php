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
        if (!Schema::hasTable('advanced_courses') || !Schema::hasColumn('advanced_courses', 'teacher_id')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->unsignedBigInteger('teacher_id')->nullable()->change();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('advanced_courses') || !Schema::hasColumn('advanced_courses', 'teacher_id')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->unsignedBigInteger('teacher_id')->nullable(false)->change();
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
