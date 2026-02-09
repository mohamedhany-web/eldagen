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
        if (!Schema::hasTable('academic_years')) {
            return;
        }

        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'code')) {
                $table->string('code', 20)->nullable()->after('name');
            }
            if (!Schema::hasColumn('academic_years', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            if (!Schema::hasColumn('academic_years', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
            if (!Schema::hasColumn('academic_years', 'color')) {
                $table->string('color', 20)->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('academic_years')) {
            return;
        }

        Schema::table('academic_years', function (Blueprint $table) {
            $columns = ['code', 'description', 'icon', 'color'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('academic_years', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
