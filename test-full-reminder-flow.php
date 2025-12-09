<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use App\Models\StudentNotification;

echo "=== TEST FULL REMINDER FLOW ===\n\n";

// 1. Cari user murid
echo "1. CARI USER MURID\n";
$user = User::whereHas('roles', function($q) {
    $q->where('name', 'murid');
})->first();

if (!$user) {
    echo "   ❌ Tidak ada user dengan role murid\n";
    exit(1);
}

echo "   ✓ User: {$user->name} ({$user->email})\n";
echo "   ✓ User ID: {$user->id}\n";

// 2. Cari murid record
$murid = Murid::where('user_id', $user->id)->first();
if (!$murid) {
    echo "   ❌ User tidak punya Murid record\n";
    exit(1);
}

echo "   ✓ Murid ID: {$murid->id}\n\n";

// 3. Cari atau buat absensi belum lengkap
echo "2. CEK/BUAT ABSENSI BELUM LENGKAP\n";
$absensi = Absensi::where('murid_id', $murid->id)
    ->whereDate('tanggal', today())
    ->where('is_complete', false)
    ->first();

if (!$absensi) {
    echo "   Tidak ada absensi belum lengkap, buat baru...\n";
    $absensi = Absensi::create([
        'murid_id' => $murid->id,
        'tanggal' => today(),
        'status' => 'hadir',
        'kelas' => $murid->kelas ?? 'Test',
        'qr_scan_done' => false,
        'manual_checkin_done' => false,
        'is_complete' => false,
    ]);
    echo "   ✓ Absensi dibuat (ID: {$absensi->id})\n";
} else {
    echo "   ✓ Absensi ditemukan (ID: {$absensi->id})\n";
}

echo "   - QR Scan: " . ($absensi->qr_scan_done ? 'YES' : 'NO') . "\n";
echo "   - Manual: " . ($absensi->manual_checkin_done ? 'YES' : 'NO') . "\n";
echo "   - Complete: " . ($absensi->is_complete ? 'YES' : 'NO') . "\n\n";

// 4. Kirim reminder (simulasi dari AbsensiResource)
echo "3. KIRIM REMINDER\n";

$missingMethod = '';
if (!$absensi->qr_scan_done) {
    $missingMethod = 'QR Scan';
} elseif (!$absensi->manual_checkin_done) {
    $missingMethod = 'Absensi Manual';
}

if (!$missingMethod) {
    echo "   ❌ Tidak ada metode yang missing\n";
    exit(0);
}

echo "   Missing Method: {$missingMethod}\n";

try {
    // Kirim ke StudentNotification
    $notification = StudentNotification::create([
        'murid_id' => $murid->id,
        'type' => 'reminder',
        'title' => 'Reminder: Lengkapi Verifikasi Absensi',
        'message' => "Anda belum melakukan {$missingMethod} untuk tanggal " . $absensi->tanggal->format('d/m/Y') . ". Segera lengkapi verifikasi Anda!",
        'data' => [
            'absensi_id' => $absensi->id,
            'tanggal' => $absensi->tanggal->format('Y-m-d'),
            'missing_method' => $missingMethod,
        ],
    ]);
    
    echo "   ✓ StudentNotification created (ID: {$notification->id})\n";
    
    // Kirim ke Filament notification
    if ($murid->user) {
        \Filament\Notifications\Notification::make()
            ->title('Reminder: Lengkapi Verifikasi Absensi')
            ->body("Anda belum melakukan {$missingMethod} untuk tanggal " . $absensi->tanggal->format('d/m/Y') . ". Segera lengkapi verifikasi Anda!")
            ->warning()
            ->sendToDatabase($murid->user);
        
        echo "   ✓ Filament Notification sent\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n";
    exit(1);
}

// 5. Verifikasi notifikasi
echo "\n4. VERIFIKASI NOTIFIKASI DI DATABASE\n";

// StudentNotification
$studentNotifCount = StudentNotification::where('murid_id', $murid->id)->count();
$studentNotifUnread = StudentNotification::where('murid_id', $murid->id)->unread()->count();
echo "   StudentNotification:\n";
echo "   - Total: {$studentNotifCount}\n";
echo "   - Unread: {$studentNotifUnread}\n";

// Filament Notification
$filamentNotifCount = \Illuminate\Notifications\DatabaseNotification::where('notifiable_id', $user->id)
    ->where('notifiable_type', 'App\Models\User')
    ->count();
$filamentNotifUnread = \Illuminate\Notifications\DatabaseNotification::where('notifiable_id', $user->id)
    ->where('notifiable_type', 'App\Models\User')
    ->whereNull('read_at')
    ->count();
echo "   Filament Notification:\n";
echo "   - Total: {$filamentNotifCount}\n";
echo "   - Unread: {$filamentNotifUnread}\n";

// 6. Tampilkan notifikasi terbaru
echo "\n5. NOTIFIKASI TERBARU (StudentNotification)\n";
$latest = StudentNotification::where('murid_id', $murid->id)
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

foreach ($latest as $notif) {
    $status = $notif->isUnread() ? '🔔 UNREAD' : '✓ READ';
    echo "   {$status} - {$notif->title}\n";
    echo "      {$notif->message}\n";
    echo "      Type: {$notif->type}\n";
    echo "      Created: {$notif->created_at->format('d/m/Y H:i:s')}\n\n";
}

echo "=== TEST SELESAI ===\n\n";
echo "LANGKAH SELANJUTNYA:\n";
echo "1. Login ke Student Panel dengan:\n";
echo "   Email: {$user->email}\n";
echo "   Password: [password yang Anda set]\n\n";
echo "2. Buka Dashboard Student Panel\n";
echo "3. Lihat widget 'Notifikasi'\n";
echo "4. Seharusnya ada {$studentNotifUnread} notifikasi unread\n";
echo "5. Jika tidak muncul, refresh halaman atau tunggu 30 detik (auto-polling)\n";
