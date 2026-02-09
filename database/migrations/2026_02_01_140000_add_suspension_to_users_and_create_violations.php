<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'suspended_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('suspended_at')->nullable();
                $table->string('suspension_reason')->nullable();
            });
        }

        Schema::create('account_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('violation_type'); // screenshot, recording, other
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('reinstated_at')->nullable();
            $table->foreignId('reinstated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_violations');
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'suspended_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['suspended_at', 'suspension_reason']);
            });
        }
    }
};
