<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Absensi;
use App\Notifications\PengajuanIzinNotification;

echo "=== TEST NOTIFICATION SIMPLE ===\n\n";

// Get admin user
$admin = User::whereHas('roles', function($query) {
    $query->where('name', 'admin');
})->first();

if (!$admin) {
    echo "❌ Admin user not found\n";
    exit;
}

echo "Admin: {$admin->name} ({$admin->email})\n\n";

// Get latest absensi with verification_status
$absensi = Absensi::whereNotNull('verification_status')->latest()->first();

if (!$absensi) {
    echo "❌ No absensi with verification_status found\n";
    exit;
}

echo "Absensi ID: {$absensi->id}\n";
echo "Status: {$absensi->status}\n";
echo "Murid: " . ($absensi->murid->name ?? 'Unknown') . "\n\n";

echo "Sending notification...\n";

try {
    $admin->notify(new PengajuanIzinNotification($absensi, $absensi->murid->name));
    echo "✓ Notification sent!\n\n";
    
    // Check database
    $notif = DB::table('notifications')
        ->where('notifiable_id', $admin->id)
        ->latest('created_at')
        ->first();
    
    if ($notif) {
        echo "✓ Notification found in database!\n";
        echo "ID: {$notif->id}\n";
        echo "Type: {$notif->type}\n";
        echo "Data: " . substr($notif->data, 0, 100) . "...\n";
    } else {
        echo "❌ Notification NOT in database\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
