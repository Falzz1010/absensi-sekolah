<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('status');
            $table->index('kelas');
            $table->index(['tanggal', 'status']);
            $table->index(['tanggal', 'kelas']);
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status']);
            $table->dropIndex(['kelas']);
            $table->dropIndex(['tanggal', 'status']);
            $table->dropIndex(['tanggal', 'kelas']);
        });
    }
};
