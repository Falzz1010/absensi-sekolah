<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Murid;
use App\Models\QrCode;
use App\Models\Setting;
use Carbon\Carbon;

echo "=== DEBUG QR SCAN ===\n\n";

// 1. Cek murid Andi
$murid = Murid::where('email', 'andi@example.com')->with('qrCode')->first();

if (!$murid) {
    echo "❌ Murid tidak ditemukan\n";
    exit(1);
}

echo "1. MURID INFO\n";
echo "   Name: {$murid->name}\n";
echo "   Email: {$murid->email}\n";
echo "   QR Code ID: " . ($murid->qr_code_id ?? 'NULL') . "\n";
echo "   Active: " . ($murid->is_active ? 'YES' : 'NO') . "\n\n";

// 2. Cek QR Code
if (!$murid->qrCode) {
    echo "2. QR CODE\n";
    echo "   ❌ QR Code tidak ditemukan\n";
    exit(1);
}

$qrCode = $murid->qrCode;
echo "2. QR CODE INFO\n";
echo "   ID: {$qrCode->id}\n";
echo "   Code: {$qrCode->code}\n";
echo "   Active: " . ($qrCode->is_active ? 'YES' : 'NO') . "\n";
echo "   Berlaku Dari: " . ($qrCode->berlaku_dari ? $qrCode->berlaku_dari->format('Y-m-d') : 'NULL') . "\n";
echo "   Berlaku Sampai: " . ($qrCode->berlaku_sampai ? $qrCode->berlaku_sampai->format('Y-m-d') : 'NULL') . "\n";

// Check validity
if ($qrCode->berlaku_dari && $qrCode->berlaku_sampai) {
    $today = now()->toDateString();
    $valid = $today >= $qrCode->berlaku_dari->toDateString() && $today <= $qrCode->berlaku_sampai->toDateString();
    echo "   Valid Today: " . ($valid ? 'YES ✓' : 'NO ✗') . "\n";
    
    if (!$valid) {
        echo "   ⚠️ QR Code expired atau belum aktif!\n";
    }
} else {
    echo "   Valid Today: YES ✓ (no date restriction)\n";
}
echo "\n";

// 3. Cek Time Window Settings
echo "3. TIME WINDOW SETTINGS\n";
$checkInStart = Setting::get('check_in_start', '06:00:00');
$checkInEnd = Setting::get('check_in_end', '08:00:00');
$lateThreshold = Setting::get('late_threshold', '07:30:00');

echo "   Check-in Start: {$checkInStart}\n";
echo "   Check-in End: {$checkInEnd}\n";
echo "   Late Threshold: {$lateThreshold}\n";

$currentTime = now()->format('H:i:s');
echo "   Current Time: {$currentTime}\n";

$withinWindow = $currentTime >= $checkInStart && $currentTime <= $checkInEnd;
echo "   Within Window: " . ($withinWindow ? 'YES ✓' : 'NO ✗') . "\n";

if (!$withinWindow) {
    echo "   ⚠️ Scan hanya dapat dilakukan pada jam {$checkInStart} - {$checkInEnd}\n";
}
echo "\n";

// 4. Cek existing absensi
echo "4. EXISTING ABSENSI TODAY\n";
$absensi = \App\Models\Absensi::where('murid_id', $murid->id)
    ->whereDate('tanggal', today())
    ->first();

if ($absensi) {
    echo "   ✓ Absensi sudah ada\n";
    echo "   Status: {$absensi->status}\n";
    echo "   QR Scan Done: " . ($absensi->qr_scan_done ? 'YES' : 'NO') . "\n";
    echo "   Manual Done: " . ($absensi->manual_checkin_done ? 'YES' : 'NO') . "\n";
    echo "   Complete: " . ($absensi->is_complete ? 'YES' : 'NO') . "\n";
    
    if ($absensi->qr_scan_done) {
        echo "   ⚠️ QR Scan sudah dilakukan pada " . Carbon::parse($absensi->qr_scan_time)->format('H:i:s') . "\n";
    }
} else {
    echo "   ✓ Belum ada absensi hari ini\n";
}
echo "\n";

// 5. Summary
echo "5. SCAN VALIDATION SUMMARY\n";
$canScan = true;
$errors = [];

if (!$murid->is_active) {
    $canScan = false;
    $errors[] = "Murid tidak aktif";
}

if (!$qrCode->is_active) {
    $canScan = false;
    $errors[] = "QR Code tidak aktif";
}

if ($qrCode->berlaku_dari && $qrCode->berlaku_sampai) {
    $today = now()->toDateString();
    if ($today < $qrCode->berlaku_dari->toDateString() || $today > $qrCode->berlaku_sampai->toDateString()) {
        $canScan = false;
        $errors[] = "QR Code expired";
    }
}

if (!$withinWindow) {
    $canScan = false;
    $errors[] = "Di luar jam check-in";
}

if ($absensi && $absensi->qr_scan_done) {
    $canScan = false;
    $errors[] = "QR Scan sudah dilakukan";
}

if ($canScan) {
    echo "   ✅ SCAN DAPAT DILAKUKAN\n";
} else {
    echo "   ❌ SCAN TIDAK DAPAT DILAKUKAN\n";
    echo "   Errors:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

echo "\n=== DEBUG SELESAI ===\n";
