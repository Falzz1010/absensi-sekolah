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
            $table->string('proof_document')->nullable()->after('keterangan');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->nullable()->after('proof_document');
            $table->foreignId('verified_by')->nullable()->after('verification_status')->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->time('check_in_time')->nullable()->after('verified_at');
            $table->boolean('is_late')->default(false)->after('check_in_time');
            $table->integer('late_duration')->nullable()->after('is_late')->comment('Duration in minutes');
            
            // Add indexes for performance
            $table->index(['murid_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropIndex(['murid_id', 'tanggal']);
            $table->dropColumn([
                'proof_document',
                'verification_status',
                'verified_by',
                'verified_at',
                'check_in_time',
                'is_late',
                'late_duration'
            ]);
        });
    }
};
