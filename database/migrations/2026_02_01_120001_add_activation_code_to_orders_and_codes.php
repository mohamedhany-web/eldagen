<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('activation_code_id')->nullable()->after('approved_by')->constrained('course_activation_codes')->nullOnDelete();
        });

        Schema::table('course_activation_codes', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('used_by')->constrained('orders')->nullOnDelete();
        });

        // إضافة 'code' لطريقة الدفع (MySQL)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash', 'other', 'code') DEFAULT 'bank_transfer'");
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['activation_code_id']);
        });
        Schema::table('course_activation_codes', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash', 'other') DEFAULT 'bank_transfer'");
        }
    }
};
