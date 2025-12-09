<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Absensi;
use App\Notifications\PengajuanIzinNotification;

echo "=== TEST NOTIFICATION REALTIME ===\n\n";

// Get all admin and guru users
$adminGuruUsers = User::whereHas('roles', function($q) {
    $q->whereIn('name', ['admin', 'guru']);
})->get();

echo "Admin/Guru Users:\n";
foreach ($adminGuruUsers as $user) {
    echo "  - {$user->name} (ID: {$user->id}, Email: {$user->email})\n";
    echo "    Unread: {$user->unreadNotifications->count()}\n";
}

echo "\n";

// Get latest pengajuan
$latestPengajuan = Absensi::whereNotNull('verification_status')
    ->whereIn('status', ['Sakit', 'Izin'])
    ->with('murid')
    ->latest()
    ->first();

if ($latestPengajuan) {
    echo "Latest Pengajuan:\n";
    echo "  ID: {$latestPengajuan->id}\n";
    echo "  Murid ID: {$latestPengajuan->murid_id}\n";
    
    if ($latestPengajuan->murid) {
        echo "  Murid: {$latestPengajuan->murid->name}\n";
        $muridName = $latestPengajuan->murid->name;
    } else {
        echo "  Murid: [NOT FOUND - using default]\n";
        $muridName = "Siswa (ID: {$latestPengajuan->murid_id})";
    }
    
    echo "  Status: {$latestPengajuan->status}\n";
    echo "  Tanggal: {$latestPengajuan->tanggal}\n";
    echo "  Verification Status: {$latestPengajuan->verification_status}\n";
    
    echo "\n--- Sending NEW notification to ALL admin/guru ---\n\n";
    
    foreach ($adminGuruUsers as $user) {
        $user->notify(new PengajuanIzinNotification(
            $latestPengajuan,
            $muridName
        ));
        
        echo "✓ Sent to {$user->name} (ID: {$user->id})\n";
    }
    
    echo "\n--- Checking notifications after send ---\n\n";
    
    foreach ($adminGuruUsers as $user) {
        $user->refresh();
        echo "{$user->name}:\n";
        echo "  Unread: {$user->unreadNotifications->count()}\n";
        echo "  Total: {$user->notifications->count()}\n";
        
        if ($user->unreadNotifications->isNotEmpty()) {
            echo "  Latest: " . $user->unreadNotifications->first()->data['title'] . "\n";
        }
        echo "\n";
    }
} else {
    echo "No pengajuan found!\n";
}

echo "=== SELESAI ===\n";
echo "\nINSTRUKSI:\n";
echo "1. Cek user mana yang sedang login di browser\n";
echo "2. Pastikan login sebagai user yang ada di list di atas\n";
echo "3. Refresh halaman admin panel (Ctrl+F5)\n";
echo "4. Klik icon bell di kanan atas\n";
echo "5. Jika masih 'No notifications', coba logout dan login lagi\n";
