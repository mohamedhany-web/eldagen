<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * وضع الدروس: strict = يجب إكمال الدرس (أو نسبة محددة) قبل التالي، flexible = الطالب يفتح أي درس
     */
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (!Schema::hasColumn('advanced_courses', 'lesson_access_mode')) {
                $table->string('lesson_access_mode', 20)->default('strict')->after('is_featured');
            }
            if (!Schema::hasColumn('advanced_courses', 'required_watch_percent')) {
                $table->unsignedTinyInteger('required_watch_percent')->default(90)->nullable()->after('lesson_access_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropColumn(['lesson_access_mode', 'required_watch_percent']);
        });
    }
};
