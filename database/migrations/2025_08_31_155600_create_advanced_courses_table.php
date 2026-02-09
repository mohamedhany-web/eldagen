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
        if (!Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('academic_subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('objectives')->nullable();
                $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
                $table->integer('duration_hours')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->string('thumbnail')->nullable();
                $table->text('requirements')->nullable();
                $table->text('what_you_learn')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->timestamps();

                $table->index(['academic_year_id', 'academic_subject_id']);
                $table->index(['is_active', 'is_featured']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advanced_courses');
    }
};
