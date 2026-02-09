<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_video_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('time_seconds')->comment('ثانية في الفيديو لظهور السؤال');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('on_wrong', 32)->default('training')->comment('restart_video | rewind_to_previous | training');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['course_lesson_id', 'time_seconds']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_video_questions');
    }
};
