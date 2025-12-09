<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECK NOTIFICATIONS TABLE ===\n\n";

try {
    $count = DB::table('notifications')->count();
    echo "✓ Table 'notifications' exists\n";
    echo "Total records: {$count}\n\n";
    
    // Show recent notifications
    $recent = DB::table('notifications')->latest('created_at')->take(5)->get();
    
    if ($recent->isNotEmpty()) {
        echo "Recent notifications:\n";
        foreach ($recent as $notif) {
            echo "ID: {$notif->id}\n";
            echo "Type: {$notif->type}\n";
            echo "Notifiable: {$notif->notifiable_type} #{$notif->notifiable_id}\n";
            echo "Read: " . ($notif->read_at ? 'Yes' : 'No') . "\n";
            echo "---\n";
        }
    } else {
        echo "No notifications found\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    // Check if table exists
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='notifications'");
    if (empty($tables)) {
        echo "\n❌ Table 'notifications' does NOT exist!\n";
        echo "Run: php artisan notifications:table\n";
        echo "Then: php artisan migrate\n";
    }
}

echo "\n=== SELESAI ===\n";
