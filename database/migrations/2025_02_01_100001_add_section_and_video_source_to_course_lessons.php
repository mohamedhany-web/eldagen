<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->foreignId('course_section_id')->nullable()->after('advanced_course_id')->constrained('course_sections')->onDelete('set null');
            $table->string('video_source')->nullable()->after('video_url'); // youtube, vimeo, google_drive, direct, other
        });
    }

    public function down(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropForeign(['course_section_id']);
            $table->dropColumn(['course_section_id', 'video_source']);
        });
    }
};
