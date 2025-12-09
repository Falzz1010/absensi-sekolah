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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // QR Global, QR Kelas X-A, dst
            $table->enum('tipe', ['global', 'kelas'])->default('global');
            $table->string('kelas')->nullable(); // Jika tipe = kelas
            $table->string('code')->unique(); // Kode unik untuk QR
            $table->text('qr_image')->nullable(); // Base64 atau path gambar QR
            $table->boolean('is_active')->default(true);
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
