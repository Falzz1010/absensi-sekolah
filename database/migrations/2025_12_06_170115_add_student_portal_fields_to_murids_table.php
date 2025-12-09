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
        Schema::table('murids', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('is_active');
            $table->foreignId('qr_code_id')->nullable()->after('photo')->constrained('qr_codes')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->after('qr_code_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('murids', function (Blueprint $table) {
            $table->dropForeign(['qr_code_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['photo', 'qr_code_id', 'user_id']);
        });
    }
};
