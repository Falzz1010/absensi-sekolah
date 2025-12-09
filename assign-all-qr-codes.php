<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Murid;
use App\Models\QrCode;

echo "=== ASSIGN QR CODES TO ALL MURIDS ===\n\n";

// Get all active murids without QR code
$murids = Murid::where('is_active', true)
    ->whereNull('qr_code_id')
    ->get();

echo "Murids without QR code: {$murids->count()}\n\n";

if ($murids->isEmpty()) {
    echo "✓ Semua murid sudah punya QR code\n";
    
    // Show all murids with QR codes
    $allMurids = Murid::where('is_active', true)->with('qrCode')->get();
    echo "\nMURIDS WITH QR CODES:\n";
    foreach ($allMurids as $murid) {
        echo "- {$murid->name}: " . ($murid->qrCode ? $murid->qrCode->code : 'NO QR CODE') . "\n";
    }
    exit(0);
}

// Get available QR codes
$qrCodes = QrCode::where('is_active', true)
    ->whereDoesntHave('murids')
    ->get();

echo "Available QR codes: {$qrCodes->count()}\n\n";

if ($qrCodes->count() < $murids->count()) {
    $needed = $murids->count() - $qrCodes->count();
    echo "⚠️ Perlu {$needed} QR code lagi\n";
    echo "Jalankan: php artisan db:seed --class=QrCodeSeeder\n";
}

// Assign QR codes
$assigned = 0;
foreach ($murids as $murid) {
    if ($qrCodes->isEmpty()) {
        echo "❌ Tidak ada QR code tersedia untuk {$murid->name}\n";
        continue;
    }
    
    $qrCode = $qrCodes->shift();
    $murid->qr_code_id = $qrCode->id;
    $murid->save();
    
    echo "✓ {$murid->name} → {$qrCode->code}\n";
    $assigned++;
}

echo "\n=== HASIL ===\n";
echo "Assigned: {$assigned}\n";
echo "Remaining murids without QR: " . ($murids->count() - $assigned) . "\n";

echo "\n=== SELESAI ===\n";
