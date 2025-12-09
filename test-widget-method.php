<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Murid;
use App\Models\StudentNotification;
use Illuminate\Support\Facades\Auth;

echo "=== TEST WIDGET METHOD ===\n\n";

// 1. Login as murid user
$user = User::where('email', 'murid@example.com')->first();

if (!$user) {
    echo "❌ User tidak ditemukan\n";
    exit(1);
}

echo "1. USER INFO\n";
echo "   ✓ User: {$user->name}\n";
echo "   ✓ Email: {$user->email}\n";
echo "   ✓ ID: {$user->id}\n";

// 2. Check murid relationship
if (!$user->murid) {
    echo "   ❌ User tidak punya murid relationship\n";
    exit(1);
}

echo "   ✓ Murid ID: {$user->murid->id}\n";
echo "   ✓ Murid Name: {$user->murid->name}\n\n";

// 3. Simulate widget getNotifications() method
echo "2. SIMULATE WIDGET METHOD\n";

// Simulate Auth::user()
Auth::login($user);

$authUser = Auth::user();
echo "   Auth user: " . ($authUser ? $authUser->name : 'NULL') . "\n";
echo "   Auth user murid: " . ($authUser && $authUser->murid ? $authUser->murid->id : 'NULL') . "\n\n";

// Get notifications like widget does
if (!$authUser || !$authUser->murid) {
    echo "   ❌ Auth user or murid not found\n";
    exit(1);
}

$notifications = StudentNotification::where('murid_id', $authUser->murid->id)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "3. NOTIFICATIONS RESULT\n";
echo "   Total: {$notifications->count()}\n\n";

if ($notifications->isEmpty()) {
    echo "   ❌ Tidak ada notifikasi ditemukan\n";
    echo "   Query: StudentNotification::where('murid_id', {$authUser->murid->id})\n";
    
    // Debug: cek langsung di database
    $directCount = StudentNotification::where('murid_id', $authUser->murid->id)->count();
    echo "   Direct count: {$directCount}\n";
    
    exit(1);
}

echo "4. NOTIFICATION DETAILS\n";
foreach ($notifications as $notif) {
    $status = $notif->isUnread() ? '🔔 UNREAD' : '✓ READ';
    echo "   {$status}\n";
    echo "   - ID: {$notif->id}\n";
    echo "   - Type: {$notif->type}\n";
    echo "   - Title: {$notif->title}\n";
    echo "   - Message: " . substr($notif->message, 0, 50) . "...\n";
    echo "   - Created: {$notif->created_at->format('d/m/Y H:i:s')}\n";
    echo "   - Diff: {$notif->created_at->diffForHumans()}\n\n";
}

// 5. Test unread count
$unreadCount = StudentNotification::where('murid_id', $authUser->murid->id)
    ->unread()
    ->count();

echo "5. UNREAD COUNT\n";
echo "   Unread: {$unreadCount}\n\n";

echo "=== TEST SELESAI ===\n\n";
echo "Widget method berfungsi dengan benar!\n";
echo "Sekarang coba:\n";
echo "1. Login ke Student Panel: http://localhost/student/login\n";
echo "2. Email: murid@example.com\n";
echo "3. Password: password (atau yang Anda set)\n";
echo "4. Lihat Dashboard → Widget Notifikasi\n";
echo "5. Seharusnya ada {$unreadCount} notifikasi unread\n";
