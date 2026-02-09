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
        Schema::table('exams', function (Blueprint $table) {
            // جعل total_marks nullable مع قيمة افتراضية
            if (Schema::hasColumn('exams', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->nullable()->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->nullable(false)->change();
            }
        });
    }
};
