<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== CHECK NOTIFICATION STRUCTURE ===\n\n";

// Get admin user
$admin = User::find(1);

if (!$admin) {
    echo "Admin user not found!\n";
    exit;
}

echo "User: {$admin->name}\n";
echo "Unread Notifications: " . $admin->unreadNotifications->count() . "\n\n";

// Check raw database structure
$notifications = DB::table('notifications')
    ->where('notifiable_id', 1)
    ->where('notifiable_type', 'App\\Models\\User')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Raw Database Notifications:\n";
foreach ($notifications as $notif) {
    echo "\nID: {$notif->id}\n";
    echo "Type: {$notif->type}\n";
    echo "Read At: " . ($notif->read_at ?? 'NULL') . "\n";
    echo "Created At: {$notif->created_at}\n";
    echo "Data: " . $notif->data . "\n";
    
    // Decode and pretty print
    $data = json_decode($notif->data, true);
    echo "\nDecoded Data:\n";
    print_r($data);
    echo "\n---\n";
}

echo "\n=== SELESAI ===\n";
