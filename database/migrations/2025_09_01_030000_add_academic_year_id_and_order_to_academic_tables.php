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
        if (Schema::hasTable('academic_years') && !Schema::hasColumn('academic_years', 'order')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->unsignedInteger('order')->default(0)->after('end_date');
            });
        }

        if (Schema::hasTable('academic_subjects')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                if (!Schema::hasColumn('academic_subjects', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')->nullable()->after('id')->constrained('academic_years')->onDelete('cascade');
                }
                if (!Schema::hasColumn('academic_subjects', 'code')) {
                    $table->string('code')->nullable()->after('name');
                }
                if (!Schema::hasColumn('academic_subjects', 'icon')) {
                    $table->string('icon')->nullable()->after('description');
                }
                if (!Schema::hasColumn('academic_subjects', 'color')) {
                    $table->string('color', 7)->nullable()->after('icon');
                }
                if (!Schema::hasColumn('academic_subjects', 'order')) {
                    $table->unsignedInteger('order')->default(0)->after('color');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('academic_years') && Schema::hasColumn('academic_years', 'order')) {
            Schema::table('academic_years', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasTable('academic_subjects')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                if (Schema::hasColumn('academic_subjects', 'academic_year_id')) {
                    $table->dropForeign(['academic_year_id']);
                    $table->dropColumn('academic_year_id');
                }
                if (Schema::hasColumn('academic_subjects', 'code')) {
                    $table->dropColumn('code');
                }
                if (Schema::hasColumn('academic_subjects', 'icon')) {
                    $table->dropColumn('icon');
                }
                if (Schema::hasColumn('academic_subjects', 'color')) {
                    $table->dropColumn('color');
                }
                if (Schema::hasColumn('academic_subjects', 'order')) {
                    $table->dropColumn('order');
                }
            });
        }
    }
};
