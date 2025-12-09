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
        Schema::table('absensis', function (Blueprint $table) {
            // Field untuk tracking metode absensi
            $table->boolean('qr_scan_done')->default(false)->after('late_duration');
            $table->timestamp('qr_scan_time')->nullable()->after('qr_scan_done');
            
            $table->boolean('manual_checkin_done')->default(false)->after('qr_scan_time');
            $table->timestamp('manual_checkin_time')->nullable()->after('manual_checkin_done');
            
            // Status verifikasi lengkap (kedua absensi sudah dilakukan)
            $table->boolean('is_complete')->default(false)->after('manual_checkin_time');
            
            // Metode absensi yang digunakan pertama kali
            $table->enum('first_method', ['qr_scan', 'manual'])->nullable()->after('is_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn([
                'qr_scan_done',
                'qr_scan_time',
                'manual_checkin_done',
                'manual_checkin_time',
                'is_complete',
                'first_method',
            ]);
        });
    }
};
