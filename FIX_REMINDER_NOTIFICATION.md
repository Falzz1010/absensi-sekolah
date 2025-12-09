# Fix: Reminder Notification Tidak Terkirim ke Student Panel

## Masalah
Ketika Admin/Guru mengirim reminder dari menu Absensi, notifikasi tidak muncul di Student Panel.

## Penyebab
Ada 2 sistem notifikasi yang berbeda:
1. **Filament Notifications** (tabel `notifications`) - Untuk bell icon di Filament
2. **StudentNotification** (tabel `student_notifications`) - Untuk widget di Student Panel

Reminder hanya mengirim ke Filament Notifications, tapi Student Panel membaca dari StudentNotification.

## Solusi
Update AbsensiResource agar reminder dikirim ke **KEDUA** sistem notifikasi:

### Yang Diubah:
**File**: `app/Filament/Resources/AbsensiResource.php`

**Perubahan**:
```php
// SEBELUM: Hanya kirim ke Filament Notification
\Filament\Notifications\Notification::make()
    ->title('Reminder: Lengkapi Verifikasi Absensi')
    ->body("...")
    ->warning()
    ->sendToDatabase($record->murid->user);

// SESUDAH: Kirim ke StudentNotification + Filament Notification
// 1. Kirim ke StudentNotification (untuk Student Panel)
\App\Models\StudentNotification::create([
    'murid_id' => $record->murid->id,
    'type' => 'reminder',
    'title' => 'Reminder: Lengkapi Verifikasi Absensi',
    'message' => "...",
    'data' => [...],
]);

// 2. Kirim ke Filament notification (untuk bell icon)
if ($record->murid->user) {
    \Filament\Notifications\Notification::make()
        ->title('Reminder: Lengkapi Verifikasi Absensi')
        ->body("...")
        ->warning()
        ->sendToDatabase($record->murid->user);
}
```

## Hasil
✅ Reminder sekarang muncul di Student Panel (NotificationsWidget)
✅ Reminder juga muncul di bell icon Filament (jika user ada)
✅ Tidak perlu user_id untuk StudentNotification (langsung ke murid_id)

## Cara Test

### Test 1: Kirim Reminder dari Admin Panel
1. Login sebagai Admin/Guru
2. Buka menu **Absensi**
3. Filter: **Belum Lengkap Hari Ini**
4. Pilih beberapa record yang belum lengkap
5. Klik **Bulk Actions → Kirim Reminder**
6. Confirm

### Test 2: Cek di Student Panel
1. Login sebagai Murid (yang dapat reminder)
2. Buka Student Panel
3. Lihat widget **Notifikasi** di dashboard
4. Seharusnya ada notifikasi reminder baru dengan icon 🔔

### Test 3: Verifikasi Database
```bash
php test-reminder-notification.php
```

Output seharusnya:
```
✓ Murid ditemukan: [Nama Murid]
✓ Notifikasi berhasil dibuat
✓ Total notifikasi untuk murid ini: [jumlah]
🔔 UNREAD - Test Reminder
```

## Catatan Penting

### Perbedaan 2 Sistem Notifikasi:

**StudentNotification** (tabel `student_notifications`):
- Untuk Student Panel custom widget
- Langsung ke murid_id (tidak perlu user_id)
- Bisa custom type, data, dll
- Digunakan oleh: NotificationsWidget, QR Scan, Manual Attendance

**Filament Notification** (tabel `notifications`):
- Untuk bell icon bawaan Filament
- Perlu user_id (polymorphic)
- Standard Filament notification
- Digunakan oleh: Bell icon di navbar

### Kapan Menggunakan Mana?

**Gunakan StudentNotification** jika:
- Notifikasi untuk Student Panel
- Perlu custom data/type
- Tidak semua murid punya user account

**Gunakan Filament Notification** jika:
- Notifikasi untuk Admin/Guru panel
- Menggunakan bell icon bawaan Filament
- User sudah pasti ada

**Gunakan KEDUANYA** jika:
- Notifikasi penting yang harus muncul di semua tempat
- Seperti reminder, alert, warning

## Troubleshooting

### Notifikasi Tidak Muncul di Student Panel
**Cek**:
1. Apakah StudentNotification record ada di database?
   ```sql
   SELECT * FROM student_notifications WHERE murid_id = [ID];
   ```
2. Apakah NotificationsWidget di-load di Student Panel?
3. Apakah murid login dengan user yang benar?

### Notifikasi Tidak Muncul di Bell Icon
**Cek**:
1. Apakah murid punya user_id?
2. Apakah Filament notification record ada?
   ```sql
   SELECT * FROM notifications WHERE notifiable_id = [USER_ID];
   ```
3. Apakah bell icon enabled di panel?

## File yang Diubah
- `app/Filament/Resources/AbsensiResource.php` - Update reminder action
- `test-reminder-notification.php` - Script test notifikasi (NEW)
- `FIX_REMINDER_NOTIFICATION.md` - Dokumentasi (NEW)

## Status
✅ **SELESAI** - Reminder sekarang terkirim ke Student Panel
