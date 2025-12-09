<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Absensi;
use App\Models\Murid;
use Carbon\Carbon;

echo "=== FIX EXISTING ABSENSI ===\n\n";

// Find Andi's murid record (check by email)
$user = \App\Models\User::where('email', 'andi@example.com')->first();
if (!$user) {
    echo "❌ User andi@example.com tidak ditemukan\n";
    exit;
}

$murid = Murid::where('user_id', $user->id)->first();

if (!$murid) {
    echo "❌ Murid tidak ditemukan\n";
    exit;
}

echo "Murid: {$murid->name}\n";
echo "Kelas: {$murid->kelas}\n\n";

// Find today's absensi
$today = Carbon::now()->toDateString();
$absensi = Absensi::where('murid_id', $murid->id)
    ->whereDate('tanggal', $today)
    ->first();

if (!$absensi) {
    echo "❌ Tidak ada absensi hari ini\n";
    exit;
}

echo "ABSENSI SAAT INI:\n";
echo "  Tanggal: {$absensi->tanggal}\n";
echo "  Status: {$absensi->status}\n";
echo "  QR Scan Done: " . ($absensi->qr_scan_done ? 'YES' : 'NO') . "\n";
echo "  QR Scan Time: " . ($absensi->qr_scan_time ?? 'NULL') . "\n";
echo "  Manual Done: " . ($absensi->manual_checkin_done ? 'YES' : 'NO') . "\n";
echo "  Manual Time: " . ($absensi->manual_checkin_time ?? 'NULL') . "\n";
echo "  Is Complete: " . ($absensi->is_complete ? 'YES' : 'NO') . "\n";
echo "  First Method: " . ($absensi->first_method ?? 'NULL') . "\n\n";

// Check if is_complete is correct
$shouldBeComplete = $absensi->qr_scan_done && $absensi->manual_checkin_done;

if ($absensi->is_complete != $shouldBeComplete) {
    echo "⚠️ MASALAH DITEMUKAN!\n";
    echo "  is_complete = " . ($absensi->is_complete ? 'TRUE' : 'FALSE') . "\n";
    echo "  Seharusnya = " . ($shouldBeComplete ? 'TRUE' : 'FALSE') . "\n\n";
    
    echo "MEMPERBAIKI...\n";
    $absensi->update(['is_complete' => $shouldBeComplete]);
    echo "✓ is_complete diperbaiki menjadi: " . ($shouldBeComplete ? 'TRUE' : 'FALSE') . "\n\n";
} else {
    echo "✓ is_complete sudah benar\n\n";
}

// Show final state
$absensi->refresh();
echo "ABSENSI SETELAH PERBAIKAN:\n";
echo "  QR Scan Done: " . ($absensi->qr_scan_done ? 'YES' : 'NO') . "\n";
echo "  Manual Done: " . ($absensi->manual_checkin_done ? 'YES' : 'NO') . "\n";
echo "  Is Complete: " . ($absensi->is_complete ? 'YES' : 'NO') . "\n\n";

if (!$absensi->is_complete) {
    echo "📝 STATUS: Belum lengkap - ";
    if (!$absensi->qr_scan_done) {
        echo "Perlu QR Scan\n";
    } else {
        echo "Perlu Absensi Manual\n";
    }
} else {
    echo "✅ STATUS: Lengkap - Kedua metode sudah dilakukan\n";
}

echo "\n=== SELESAI ===\n";
