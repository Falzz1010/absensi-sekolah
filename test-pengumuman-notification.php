<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pengumuman;
use App\Models\User;

echo "=== TEST PENGUMUMAN NOTIFICATION ===\n\n";

// Count students
$students = User::whereHas('roles', function($query) {
    $query->where('name', 'murid');
})->get();

echo "Total Siswa: " . $students->count() . "\n\n";

// Test 1: Create pengumuman for all students
echo "=== TEST 1: Pengumuman untuk Semua Siswa ===\n";
$pengumuman1 = Pengumuman::create([
    'judul' => 'Ujian Tengah Semester',
    'isi' => '<p>Ujian Tengah Semester akan dilaksanakan pada tanggal <strong>15-20 Desember 2025</strong>.</p><p>Harap semua siswa mempersiapkan diri dengan baik.</p>',
    'prioritas' => 'tinggi',
    'target' => 'semua',
    'created_by' => 1,
    'is_active' => true,
    'published_at' => now(),
]);

echo "✓ Pengumuman created: {$pengumuman1->judul}\n";
echo "  Prioritas: {$pengumuman1->prioritas}\n";
echo "  Target: {$pengumuman1->target}\n\n";

sleep(1);

// Check notifications
echo "Checking notifications (first 3 students)...\n\n";
foreach ($students->take(3) as $student) {
    $student->refresh();
    $unreadCount = $student->unreadNotifications()->count();
    echo "Student: {$student->name}\n";
    echo "  Unread: {$unreadCount}\n";
    
    $latestNotif = $student->unreadNotifications()->latest()->first();
    if ($latestNotif) {
        echo "  Latest: " . ($latestNotif->data['title'] ?? 'No title') . "\n";
    }
    echo "\n";
}

// Test 2: Create pengumuman for specific class
echo "=== TEST 2: Pengumuman untuk Kelas Tertentu ===\n";
$pengumuman2 = Pengumuman::create([
    'judul' => 'Kunjungan Industri Kelas X IPA 1',
    'isi' => '<p>Kunjungan industri untuk kelas X IPA 1 akan dilaksanakan pada hari Jumat, 13 Desember 2025.</p>',
    'prioritas' => 'sedang',
    'target' => 'kelas_tertentu',
    'kelas_target' => 'X IPA 1',
    'created_by' => 1,
    'is_active' => true,
    'published_at' => now(),
]);

echo "✓ Pengumuman created: {$pengumuman2->judul}\n";
echo "  Prioritas: {$pengumuman2->prioritas}\n";
echo "  Target: {$pengumuman2->target}\n";
echo "  Kelas: {$pengumuman2->kelas_target}\n\n";

sleep(1);

// Check specific class students
$kelasStudents = User::whereHas('roles', function($query) {
    $query->where('name', 'murid');
})->whereHas('murid', function($query) {
    $query->where('kelas', 'X IPA 1');
})->get();

echo "Students in X IPA 1: " . $kelasStudents->count() . "\n";
if ($kelasStudents->isNotEmpty()) {
    $firstStudent = $kelasStudents->first();
    $firstStudent->refresh();
    echo "  {$firstStudent->name}: " . $firstStudent->unreadNotifications()->count() . " unread\n";
}

echo "\n=== CLEANUP ===\n";
$pengumuman1->delete();
$pengumuman2->delete();
echo "✓ Test pengumuman deleted\n";

echo "\n=== SELESAI ===\n";
echo "\nINSTRUKSI:\n";
echo "1. Login ke admin panel\n";
echo "2. Buka menu 'Pengumuman'\n";
echo "3. Klik 'New Pengumuman'\n";
echo "4. Isi form dan klik 'Create'\n";
echo "5. Login ke student panel\n";
echo "6. Cek bell icon → Harus ada notifikasi baru\n";
