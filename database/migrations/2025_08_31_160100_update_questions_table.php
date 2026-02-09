<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('questions')) {
            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            // إضافة category_id فقط إذا وُجد جدول question_categories
            if (Schema::hasTable('question_categories') && !Schema::hasColumn('questions', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('question_bank_id')->constrained('question_categories')->onDelete('set null');
            } elseif (!Schema::hasColumn('questions', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('question_bank_id');
            }

            if (!Schema::hasColumn('questions', 'difficulty_level')) {
                $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium')->after('points');
            }
            if (!Schema::hasColumn('questions', 'image_url')) {
                $table->string('image_url')->nullable()->after('difficulty_level');
            }
            if (!Schema::hasColumn('questions', 'audio_url')) {
                $table->string('audio_url')->nullable()->after('image_url');
            }
            if (!Schema::hasColumn('questions', 'video_url')) {
                $table->string('video_url')->nullable()->after('audio_url');
            }
            if (!Schema::hasColumn('questions', 'time_limit')) {
                $table->integer('time_limit')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('questions', 'tags')) {
                $table->json('tags')->nullable()->after('time_limit');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'points')) {
                $table->decimal('points', 5, 2)->default(1.00)->change();
            }
            if (Schema::hasColumn('questions', 'correct_answer')) {
                $table->json('correct_answer')->change();
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'category_id') && !$this->indexExists('questions', 'questions_category_id_type_index')) {
                $table->index(['category_id', 'type']);
            }
            if (Schema::hasColumn('questions', 'difficulty_level') && !$this->indexExists('questions', 'questions_difficulty_level_is_active_index')) {
                $table->index(['difficulty_level', 'is_active']);
            }
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        $indexes = Schema::getIndexListing($table);
        return in_array($name, $indexes);
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id', 'difficulty_level', 'image_url', 
                'audio_url', 'video_url', 'time_limit', 'tags'
            ]);
        });
    }
};
