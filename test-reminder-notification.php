<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Murid;
use App\Models\StudentNotification;

echo "=== TEST REMINDER NOTIFICATION ===\n\n";

// Cari murid dengan user_id
$murid = Murid::whereNotNull('user_id')->first();

if (!$murid) {
    echo "❌ Tidak ada murid dengan user_id\n";
    exit(1);
}

echo "✓ Murid ditemukan: {$murid->name} (ID: {$murid->id})\n";
echo "  User ID: {$murid->user_id}\n";
echo "  Email: {$murid->email}\n\n";

// Buat test notification
$notification = StudentNotification::create([
    'murid_id' => $murid->id,
    'type' => 'reminder',
    'title' => 'Test Reminder',
    'message' => 'Ini adalah test reminder untuk verifikasi sistem notifikasi.',
    'data' => [
        'test' => true,
        'created_at' => now()->toDateTimeString(),
    ],
]);

echo "✓ Notifikasi berhasil dibuat (ID: {$notification->id})\n\n";

// Cek notifikasi di database
$count = StudentNotification::where('murid_id', $murid->id)->count();
echo "✓ Total notifikasi untuk murid ini: {$count}\n\n";

// Tampilkan 5 notifikasi terakhir
echo "=== 5 NOTIFIKASI TERAKHIR ===\n";
$notifications = StudentNotification::where('murid_id', $murid->id)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($notifications as $notif) {
    $status = $notif->isUnread() ? '🔔 UNREAD' : '✓ READ';
    echo "{$status} - {$notif->title}\n";
    echo "   {$notif->message}\n";
    echo "   Created: {$notif->created_at->format('d/m/Y H:i:s')}\n\n";
}

echo "=== TEST SELESAI ===\n";
