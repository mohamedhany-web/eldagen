<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * إضافة نوع سؤال جديد: سؤال عبارة عن صورة مع اختيار متعدد
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
                'multiple_choice', 'true_false', 'essay', 'fill_blank',
                'short_answer', 'matching', 'ordering', 'image_multiple_choice'
            ) NOT NULL DEFAULT 'multiple_choice'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // تحويل الأسئلة من النوع الجديد إلى multiple_choice قبل الإزالة
            DB::table('questions')->where('type', 'image_multiple_choice')->update(['type' => 'multiple_choice']);

            DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM(
                'multiple_choice', 'true_false', 'essay', 'fill_blank',
                'short_answer', 'matching', 'ordering'
            ) NOT NULL DEFAULT 'multiple_choice'");
        }
    }
};
