<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HariLibur;
use App\Models\User;

echo "=== TEST HARI LIBUR NOTIFICATION ===\n\n";

// Count students
$students = User::whereHas('roles', function($query) {
    $query->where('name', 'murid');
})->get();

echo "Total Siswa: " . $students->count() . "\n\n";

// Create test hari libur
echo "Creating test hari libur...\n";
$hariLibur = HariLibur::create([
    'nama' => 'Test Hari Libur - ' . now()->format('Y-m-d H:i:s'),
    'tanggal' => now()->addDays(7),
    'keterangan' => 'Ini adalah test pengumuman hari libur untuk siswa',
]);

echo "✓ Hari Libur created: {$hariLibur->nama}\n";
echo "  Tanggal: " . $hariLibur->tanggal->format('d M Y') . "\n\n";

// Wait a moment for observer to process
sleep(1);

// Check notifications
echo "Checking notifications...\n\n";

foreach ($students->take(5) as $student) {
    $unreadCount = $student->unreadNotifications()->count();
    echo "Student: {$student->name}\n";
    echo "  Unread Notifications: {$unreadCount}\n";
    
    $latestNotif = $student->unreadNotifications()->latest()->first();
    if ($latestNotif) {
        echo "  Latest: " . ($latestNotif->data['title'] ?? 'No title') . "\n";
        echo "  Body: " . ($latestNotif->data['body'] ?? 'No body') . "\n";
    }
    echo "\n";
}

echo "=== TEST UPDATE ===\n\n";

// Update hari libur
$hariLibur->update([
    'keterangan' => 'Keterangan telah diperbarui - ' . now()->format('H:i:s'),
]);

echo "✓ Hari Libur updated\n\n";

sleep(1);

// Check notifications again
$firstStudent = $students->first();
if ($firstStudent) {
    $firstStudent->refresh();
    echo "Student: {$firstStudent->name}\n";
    echo "  Total Unread: " . $firstStudent->unreadNotifications()->count() . "\n";
    echo "  Latest 2 notifications:\n";
    
    foreach ($firstStudent->unreadNotifications()->latest()->take(2)->get() as $notif) {
        echo "    - " . ($notif->data['title'] ?? 'No title') . "\n";
        echo "      " . ($notif->data['body'] ?? 'No body') . "\n";
    }
}

echo "\n=== CLEANUP ===\n";
$hariLibur->delete();
echo "✓ Test hari libur deleted\n";

echo "\n=== SELESAI ===\n";
echo "\nINSTRUKSI:\n";
echo "1. Login ke student panel sebagai murid\n";
echo "2. Cek notifikasi di bell icon\n";
echo "3. Seharusnya ada notifikasi 'Pengumuman Hari Libur'\n";
echo "4. Atau buat hari libur baru dari admin panel\n";
