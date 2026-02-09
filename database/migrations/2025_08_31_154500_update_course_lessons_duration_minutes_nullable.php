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
        if (Schema::hasTable('lessons') && Schema::hasColumn('lessons', 'duration_minutes')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->integer('duration_minutes')->nullable()->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lessons') && Schema::hasColumn('lessons', 'duration_minutes')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->integer('duration_minutes')->nullable(false)->change();
            });
        }
    }
};
