<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Murid;
use App\Models\QrCode;

echo "=== ASSIGN QR CODE TO MURID ===\n\n";

// 1. Cek murid Andi
$murid = Murid::where('email', 'andi@example.com')->first();

if (!$murid) {
    echo "❌ Murid Andi tidak ditemukan\n";
    exit(1);
}

echo "1. MURID INFO\n";
echo "   ID: {$murid->id}\n";
echo "   Name: {$murid->name}\n";
echo "   Email: {$murid->email}\n";
echo "   QR Code ID: " . ($murid->qr_code_id ?? 'NULL') . "\n\n";

// 2. Cek QR codes yang tersedia
$qrCodes = QrCode::where('is_active', true)->get();
echo "2. AVAILABLE QR CODES\n";
echo "   Total: {$qrCodes->count()}\n";

if ($qrCodes->isEmpty()) {
    echo "   ❌ Tidak ada QR code aktif\n";
    echo "   Jalankan: php artisan db:seed --class=QrCodeSeeder\n";
    exit(1);
}

foreach ($qrCodes as $qr) {
    $assigned = Murid::where('qr_code_id', $qr->id)->first();
    echo "   - ID: {$qr->id}, Code: {$qr->code}, Assigned to: " . ($assigned ? $assigned->name : 'NONE') . "\n";
}
echo "\n";

// 3. Assign QR code ke Andi jika belum punya
if (!$murid->qr_code_id) {
    // Cari QR code yang belum di-assign
    $availableQr = null;
    foreach ($qrCodes as $qr) {
        $assigned = Murid::where('qr_code_id', $qr->id)->first();
        if (!$assigned) {
            $availableQr = $qr;
            break;
        }
    }
    
    if (!$availableQr) {
        echo "3. ASSIGN QR CODE\n";
        echo "   ❌ Semua QR code sudah di-assign\n";
        echo "   Buat QR code baru atau unassign dari murid lain\n";
        exit(1);
    }
    
    $murid->qr_code_id = $availableQr->id;
    $murid->save();
    
    echo "3. ASSIGN QR CODE\n";
    echo "   ✓ QR Code assigned!\n";
    echo "   QR Code ID: {$availableQr->id}\n";
    echo "   QR Code: {$availableQr->code}\n\n";
} else {
    $qrCode = QrCode::find($murid->qr_code_id);
    echo "3. QR CODE STATUS\n";
    echo "   ✓ Murid sudah punya QR code\n";
    echo "   QR Code ID: {$qrCode->id}\n";
    echo "   QR Code: {$qrCode->code}\n";
    echo "   Active: " . ($qrCode->is_active ? 'YES' : 'NO') . "\n\n";
}

// 4. Test QR code
$qrCode = QrCode::find($murid->qr_code_id);
echo "4. TEST QR CODE\n";
echo "   Scan QR code dengan text: {$qrCode->code}\n";
echo "   Atau generate QR code di: http://127.0.0.1:8000/admin/qr-codes/{$qrCode->id}\n\n";

echo "=== SELESAI ===\n";
