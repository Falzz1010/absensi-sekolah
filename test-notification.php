<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Filament\Notifications\Notification;

echo "=== TEST NOTIFICATION ===\n\n";

// Get admin users
echo "1. CHECKING ADMIN/GURU USERS\n";
$adminUsers = User::whereHas('roles', function($query) {
    $query->whereIn('name', ['admin', 'guru']);
})->get();

echo "Total Admin/Guru: " . $adminUsers->count() . "\n\n";

if ($adminUsers->isEmpty()) {
    echo "❌ Tidak ada user dengan role admin/guru\n";
    echo "Checking all users...\n\n";
    
    $allUsers = User::with('roles')->get();
    foreach ($allUsers as $user) {
        echo "User: {$user->name} ({$user->email})\n";
        echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
        echo "---\n";
    }
    exit;
}

foreach ($adminUsers as $user) {
    echo "- {$user->name} ({$user->email})\n";
    echo "  Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
}

echo "\n2. SENDING TEST NOTIFICATION\n";
$testUser = $adminUsers->first();
echo "Sending to: {$testUser->name}\n";

try {
    $testUser->notify(
        Notification::make()
            ->title('Test Notification')
            ->body('This is a test notification from script')
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->toDatabase()
    );
    
    echo "✓ Notification sent!\n\n";
    
    // Check if notification was saved
    $notifications = \Illuminate\Notifications\DatabaseNotification::where('notifiable_id', $testUser->id)
        ->latest()
        ->first();
    
    if ($notifications) {
        echo "✓ Notification found in database!\n";
        echo "ID: {$notifications->id}\n";
        echo "Type: {$notifications->type}\n";
        echo "Read: " . ($notifications->read_at ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Notification NOT found in database\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== SELESAI ===\n";
