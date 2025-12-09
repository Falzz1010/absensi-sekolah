# Cara Test Reminder Notification

## Persiapan

### 1. Pastikan Data Sudah Ada
```bash
# Cek user murid
php artisan tinker
>>> App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'murid'))->get(['id', 'name', 'email']);

# Cek murid record
>>> App\Models\Murid::whereNotNull('user_id')->get(['id', 'name', 'user_id']);
```

### 2. Jalankan Test Script
```bash
php test-full-reminder-flow.php
```

Output seharusnya:
```
✓ User: Murid Satu (murid@example.com)
✓ Murid ID: 1
✓ Absensi dibuat (ID: 156)
✓ StudentNotification created (ID: 4)
✓ Filament Notification sent

StudentNotification:
- Total: 2
- Unread: 2
```

## Cara Test dari Admin Panel

### Step 1: Buat Absensi Belum Lengkap
1. Login sebagai Admin
2. Buka menu **Absensi → Create**
3. Isi form:
   - Nama Murid: Pilih murid yang punya user account (contoh: Murid Satu)
   - Tanggal: Hari ini
   - Status: Hadir
   - Kelas: (auto-fill dari murid)
4. Klik **Create**
5. Absensi akan dibuat dengan status belum lengkap (qr_scan_done = false, manual_checkin_done = false)

### Step 2: Kirim Reminder
1. Masih di menu **Absensi**
2. Gunakan filter: **Status Verifikasi → Belum Lengkap**
3. Atau filter: **Belum Lengkap Hari Ini → Yes**
4. Pilih (checkbox) absensi yang belum lengkap
5. Klik **Bulk Actions** (dropdown di atas tabel)
6. Pilih **Kirim Reminder**
7. Confirm di modal
8. Seharusnya muncul notifikasi: "Berhasil mengirim X reminder ke siswa"

### Step 3: Verifikasi di Database
```bash
php artisan tinker
>>> App\Models\StudentNotification::latest()->first();
```

Seharusnya ada notifikasi dengan:
- type: 'reminder'
- title: 'Reminder: Lengkapi Verifikasi Absensi'
- message: 'Anda belum melakukan QR Scan/Absensi Manual...'

## Cara Cek di Student Panel

### Step 1: Login sebagai Murid
1. Logout dari Admin Panel
2. Buka `/student/login`
3. Login dengan:
   - Email: `murid@example.com`
   - Password: `password` (atau password yang Anda set)

### Step 2: Cek Dashboard
1. Setelah login, Anda akan masuk ke Dashboard Student Panel
2. Lihat widget **Notifikasi** (biasanya di bagian atas)
3. Seharusnya ada badge merah dengan angka notifikasi unread
4. Notifikasi reminder seharusnya muncul dengan:
   - Icon: 🔔 (bell-alert, warna kuning)
   - Title: "Reminder: Lengkapi Verifikasi Absensi"
   - Message: "Anda belum melakukan QR Scan untuk tanggal..."
   - Background: Biru muda (karena unread)

### Step 3: Jika Tidak Muncul
1. **Refresh halaman** (Ctrl+F5 atau F5)
2. **Tunggu 30 detik** - Widget auto-refresh setiap 30 detik
3. **Clear cache browser**
4. **Cek console browser** (F12) untuk error JavaScript
5. **Cek log Laravel**:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Cari log: "NotificationsWidget: Loaded notifications"

## Troubleshooting

### Notifikasi Tidak Terkirim dari Admin Panel

**Cek 1: Apakah murid punya user_id?**
```bash
php artisan tinker
>>> $murid = App\Models\Murid::find([ID]);
>>> $murid->user_id; // Harus ada nilai, bukan null
```

**Solusi**: Jalankan sync command
```bash
php artisan murid:sync-users
```

**Cek 2: Apakah absensi benar-benar belum lengkap?**
```bash
>>> $absensi = App\Models\Absensi::find([ID]);
>>> $absensi->is_complete; // Harus false
>>> $absensi->qr_scan_done; // Harus false atau
>>> $absensi->manual_checkin_done; // Harus false
```

**Cek 3: Apakah ada error saat kirim?**
- Cek log Laravel: `storage/logs/laravel.log`
- Cek response di browser Network tab (F12)

### Notifikasi Tidak Muncul di Student Panel

**Cek 1: Apakah notifikasi ada di database?**
```bash
php artisan tinker
>>> App\Models\StudentNotification::where('murid_id', [MURID_ID])->count();
>>> App\Models\StudentNotification::where('murid_id', [MURID_ID])->latest()->first();
```

**Cek 2: Apakah user login dengan murid yang benar?**
```bash
>>> $user = App\Models\User::where('email', 'murid@example.com')->first();
>>> $user->murid; // Harus ada
>>> $user->murid->id; // Harus sama dengan murid_id di notifikasi
```

**Cek 3: Apakah widget di-load?**
- Buka Student Panel Dashboard
- Buka browser console (F12)
- Cek apakah ada error
- Cek Network tab untuk request ke `/student/widgets/...`

**Cek 4: Apakah method getNotifications() dipanggil?**
- Cek log Laravel untuk: "NotificationsWidget: Loaded notifications"
- Jika tidak ada log, berarti widget tidak di-load
- Jika ada log tapi count = 0, berarti query tidak menemukan notifikasi

### Widget Tidak Auto-Refresh

**Cek**: Apakah polling enabled?
```php
// Di NotificationsWidget.php
protected static ?string $pollingInterval = '30s';
```

**Cek**: Apakah Livewire berfungsi?
- Buka browser console
- Cek apakah ada error Livewire
- Cek Network tab untuk request Livewire polling

## Debug Script

### Test Notifikasi Manual
```bash
php test-full-reminder-flow.php
```

### Test Reminder dari Absensi Specific
```bash
php artisan tinker
>>> $absensi = App\Models\Absensi::find([ID]);
>>> $murid = $absensi->murid;
>>> App\Models\StudentNotification::create([
...     'murid_id' => $murid->id,
...     'type' => 'reminder',
...     'title' => 'Test Reminder',
...     'message' => 'Test message',
...     'data' => [],
... ]);
```

### Cek Log Real-time
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Wait -Tail 50

# Linux/Mac
tail -f storage/logs/laravel.log
```

## Checklist Test Lengkap

- [ ] User murid ada dan punya role 'murid'
- [ ] Murid record ada dan punya user_id
- [ ] Absensi belum lengkap dibuat (is_complete = false)
- [ ] Reminder dikirim dari Admin Panel (bulk action)
- [ ] StudentNotification record dibuat di database
- [ ] Login ke Student Panel dengan user murid
- [ ] Dashboard Student Panel terbuka
- [ ] Widget Notifikasi muncul
- [ ] Notifikasi reminder muncul di widget
- [ ] Badge unread count muncul
- [ ] Klik "Tandai Dibaca" berfungsi
- [ ] Notifikasi berubah dari unread ke read

## Status Saat Ini

✅ Kode reminder sudah benar di AbsensiResource
✅ StudentNotification model dan migration sudah ada
✅ NotificationsWidget sudah terdaftar di StudentPanelProvider
✅ Widget sudah ada polling 30 detik
✅ View sudah support type 'reminder' dengan icon bell-alert
✅ Test script berhasil membuat notifikasi di database

⚠️ **Perlu dicek**: Apakah widget benar-benar muncul di Student Panel saat login
