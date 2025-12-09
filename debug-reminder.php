<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\StudentNotification;

echo "=== DEBUG REMINDER SYSTEM ===\n\n";

// 1. Cek absensi yang belum lengkap
echo "1. CEK ABSENSI BELUM LENGKAP\n";
$incompleteAbsensi = Absensi::where('is_complete', false)
    ->whereDate('tanggal', today())
    ->with('murid')
    ->get();

echo "   Total absensi belum lengkap hari ini: {$incompleteAbsensi->count()}\n\n";

if ($incompleteAbsensi->isEmpty()) {
    echo "   ❌ Tidak ada absensi belum lengkap. Buat dulu absensi untuk test.\n";
    exit(0);
}

// 2. Detail setiap absensi
echo "2. DETAIL ABSENSI BELUM LENGKAP\n";
foreach ($incompleteAbsensi as $absensi) {
    echo "   - ID: {$absensi->id}\n";
    echo "     Tanggal: {$absensi->tanggal->format('d/m/Y')}\n";
    echo "     Murid: " . ($absensi->murid ? $absensi->murid->name : 'NULL') . "\n";
    echo "     Murid ID: " . ($absensi->murid ? $absensi->murid->id : 'NULL') . "\n";
    echo "     QR Scan: " . ($absensi->qr_scan_done ? 'YES' : 'NO') . "\n";
    echo "     Manual: " . ($absensi->manual_checkin_done ? 'YES' : 'NO') . "\n";
    echo "     Complete: " . ($absensi->is_complete ? 'YES' : 'NO') . "\n";
    
    if ($absensi->murid) {
        echo "     User ID: " . ($absensi->murid->user_id ?? 'NULL') . "\n";
    }
    echo "\n";
}

// 3. Simulasi kirim reminder ke absensi pertama
echo "3. SIMULASI KIRIM REMINDER\n";
$testAbsensi = $incompleteAbsensi->first();

if (!$testAbsensi->murid) {
    echo "   ❌ Absensi tidak punya murid\n";
    exit(1);
}

echo "   Target: {$testAbsensi->murid->name} (Murid ID: {$testAbsensi->murid->id})\n";

// Tentukan metode yang belum dilakukan
$missingMethod = '';
if (!$testAbsensi->qr_scan_done) {
    $missingMethod = 'QR Scan';
} elseif (!$testAbsensi->manual_checkin_done) {
    $missingMethod = 'Absensi Manual';
}

echo "   Missing Method: {$missingMethod}\n\n";

if (!$missingMethod) {
    echo "   ❌ Tidak ada metode yang missing (sudah lengkap?)\n";
    exit(0);
}

// Kirim notifikasi
echo "4. KIRIM NOTIFIKASI\n";
try {
    $notification = StudentNotification::create([
        'murid_id' => $testAbsensi->murid->id,
        'type' => 'reminder',
        'title' => 'Reminder: Lengkapi Verifikasi Absensi',
        'message' => "Anda belum melakukan {$missingMethod} untuk tanggal " . $testAbsensi->tanggal->format('d/m/Y') . ". Segera lengkapi verifikasi Anda!",
        'data' => [
            'absensi_id' => $testAbsensi->id,
            'tanggal' => $testAbsensi->tanggal->format('Y-m-d'),
            'missing_method' => $missingMethod,
        ],
    ]);
    
    echo "   ✓ StudentNotification created (ID: {$notification->id})\n";
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n";
    exit(1);
}

// 5. Verifikasi notifikasi tersimpan
echo "\n5. VERIFIKASI NOTIFIKASI\n";
$count = StudentNotification::where('murid_id', $testAbsensi->murid->id)->count();
echo "   Total notifikasi untuk murid ini: {$count}\n";

$latest = StudentNotification::where('murid_id', $testAbsensi->murid->id)
    ->orderBy('created_at', 'desc')
    ->first();

if ($latest) {
    echo "   Latest notification:\n";
    echo "   - Title: {$latest->title}\n";
    echo "   - Message: {$latest->message}\n";
    echo "   - Created: {$latest->created_at->format('d/m/Y H:i:s')}\n";
    echo "   - Read: " . ($latest->isUnread() ? 'NO (UNREAD)' : 'YES') . "\n";
}

echo "\n=== DEBUG SELESAI ===\n";
echo "\nSekarang login sebagai murid '{$testAbsensi->murid->name}' dan cek Student Panel.\n";
