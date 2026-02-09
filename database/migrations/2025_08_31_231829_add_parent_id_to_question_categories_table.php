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
        if (!Schema::hasTable('question_categories')) {
            return;
        }
        Schema::table('question_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('question_categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('academic_subject_id')->constrained('question_categories')->onDelete('cascade');
            }
            $indexName = 'question_categories_parent_id_order_index';
            $indexes = Schema::getIndexListing('question_categories');
            if (!in_array($indexName, $indexes)) {
                $table->index(['parent_id', 'order'], $indexName);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('question_categories') || !Schema::hasColumn('question_categories', 'parent_id')) {
            return;
        }
        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex('question_categories_parent_id_order_index');
            $table->dropColumn('parent_id');
        });
    }
};
