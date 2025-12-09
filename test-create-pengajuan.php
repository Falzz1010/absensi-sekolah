<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Absensi;
use App\Models\Murid;

echo "=== TEST CREATE PENGAJUAN ===\n\n";

// Get murid
$murid = Murid::where('user_id', '!=', null)->first();

if (!$murid) {
    echo "❌ Murid tidak ditemukan\n";
    exit;
}

echo "Murid: {$murid->name}\n";
echo "Kelas: {$murid->kelas}\n\n";

// Create test pengajuan
try {
    $absensi = Absensi::create([
        'murid_id' => $murid->id,
        'tanggal' => now()->toDateString(),
        'status' => 'Sakit',
        'kelas' => $murid->kelas,
        'keterangan' => 'Test pengajuan sakit - demam tinggi',
        'proof_document' => 'attendance-proofs/test.png',
        'verification_status' => 'pending',
    ]);
    
    echo "✓ Pengajuan berhasil dibuat!\n\n";
    echo "ID: {$absensi->id}\n";
    echo "Status: {$absensi->status}\n";
    echo "Verification Status: {$absensi->verification_status}\n";
    echo "Proof: {$absensi->proof_document}\n\n";
    
    // Check if it appears in query
    $check = Absensi::whereNotNull('verification_status')
        ->whereIn('status', ['Sakit', 'Izin'])
        ->where('id', $absensi->id)
        ->first();
    
    if ($check) {
        echo "✓ Data muncul di query resource!\n";
    } else {
        echo "❌ Data TIDAK muncul di query resource\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
