<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\QrCode;
use App\Models\Setting;
use Carbon\Carbon;

echo "=== FIX QR SCAN ISSUES ===\n\n";

// 1. Extend QR Code validity to 1 year
echo "1. EXTENDING QR CODE VALIDITY\n";
$qrCode = QrCode::find(6);
if ($qrCode) {
    $qrCode->update([
        'berlaku_dari' => Carbon::now()->startOfDay(),
        'berlaku_sampai' => Carbon::now()->addYear()->endOfDay(),
    ]);
    echo "   ✓ QR Code berlaku dari: " . $qrCode->berlaku_dari . "\n";
    echo "   ✓ QR Code berlaku sampai: " . $qrCode->berlaku_sampai . "\n";
} else {
    echo "   ✗ QR Code tidak ditemukan\n";
}

echo "\n2. EXTENDING CHECK-IN TIME WINDOW\n";
// Extend check-in window to 24 hours for testing
$checkInStart = Setting::where('key', 'check_in_start')->first();
if ($checkInStart) {
    $checkInStart->update(['value' => '00:00:00']);
    echo "   ✓ Check-in Start updated: 00:00:00\n";
} else {
    Setting::create([
        'key' => 'check_in_start',
        'value' => '00:00:00',
        'type' => 'time',
        'group' => 'absensi',
        'label' => 'Jam Mulai Check-in',
        'description' => 'Waktu mulai check-in absensi',
    ]);
    echo "   ✓ Check-in Start created: 00:00:00\n";
}

$checkInEnd = Setting::where('key', 'check_in_end')->first();
if ($checkInEnd) {
    $checkInEnd->update(['value' => '23:59:59']);
    echo "   ✓ Check-in End updated: 23:59:59\n";
} else {
    Setting::create([
        'key' => 'check_in_end',
        'value' => '23:59:59',
        'type' => 'time',
        'group' => 'absensi',
        'label' => 'Jam Akhir Check-in',
        'description' => 'Waktu akhir check-in absensi',
    ]);
    echo "   ✓ Check-in End created: 23:59:59\n";
}
echo "   ℹ️ Sekarang QR scan bisa dilakukan kapan saja (24 jam)\n";

echo "\n3. CURRENT TIME\n";
echo "   Current Time: " . Carbon::now()->format('H:i:s') . "\n";
echo "   ✓ Sekarang dalam window check-in\n";

echo "\n=== FIX SELESAI ===\n";
echo "\nSilakan coba scan QR code lagi!\n";
