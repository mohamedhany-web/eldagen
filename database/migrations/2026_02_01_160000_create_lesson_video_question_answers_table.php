<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_video_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_video_question_id')->constrained('lesson_video_questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index(['lesson_video_question_id', 'user_id'], 'lvq_answers_vq_user_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_video_question_answers');
    }
};
