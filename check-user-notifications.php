<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== CHECK USER NOTIFICATIONS ===\n\n";

// Get all users with their notifications
$users = User::with('notifications')->get();

foreach ($users as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    echo "ID: {$user->id}\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "Unread Notifications: " . $user->unreadNotifications->count() . "\n";
    echo "Total Notifications: " . $user->notifications->count() . "\n";
    
    if ($user->notifications->isNotEmpty()) {
        echo "\nNotifications:\n";
        foreach ($user->notifications->take(3) as $notif) {
            echo "  - " . ($notif->data['title'] ?? 'No title') . "\n";
            echo "    Read: " . ($notif->read_at ? 'Yes' : 'No') . "\n";
            echo "    Created: {$notif->created_at}\n";
        }
    }
    
    echo "\n---\n\n";
}

echo "=== SELESAI ===\n";
