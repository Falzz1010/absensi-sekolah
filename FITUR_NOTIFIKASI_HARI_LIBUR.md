# ✅ Fitur Notifikasi Hari Libur untuk Siswa

## 🎯 Fitur Baru

Ketika admin atau guru membuat/update hari libur, **semua siswa** akan otomatis menerima notifikasi.

## 📋 Komponen yang Dibuat

### 1. Notification Class
**File**: `app/Notifications/HariLiburNotification.php`

Mengirim notifikasi dengan format:
- **Title**: "Pengumuman Hari Libur" (create) atau "Update Hari Libur" (update)
- **Body**: Nama hari libur + tanggal + keterangan
- **Icon**: Calendar icon dengan warna warning

### 2. Observer Class
**File**: `app/Observers/HariLiburObserver.php`

Menangani event:
- `created`: Kirim notifikasi ke semua siswa saat hari libur dibuat
- `updated`: Kirim notifikasi ke semua siswa saat hari libur diupdate
- `deleted`: Tidak kirim notifikasi (untuk menghindari kebingungan)

### 3. Registration
**File**: `app/Providers/AppServiceProvider.php`

Observer didaftarkan di `boot()` method.

## 🎬 Cara Kerja

### Skenario 1: Admin Membuat Hari Libur Baru
```
1. Admin login ke admin panel
2. Buka menu "Hari Libur" 
3. Klik "Create"
4. Isi form:
   - Nama: "Hari Raya Idul Fitri"
   - Tanggal: 2025-04-01
   - Keterangan: "Libur nasional"
5. Klik "Create"

→ OTOMATIS: Semua siswa dapat notifikasi
```

### Skenario 2: Guru Update Hari Libur
```
1. Guru login ke admin panel
2. Buka menu "Hari Libur"
3. Edit hari libur yang sudah ada
4. Update keterangan
5. Klik "Save"

→ OTOMATIS: Semua siswa dapat notifikasi update
```

### Skenario 3: Siswa Menerima Notifikasi
```
1. Siswa login ke student panel
2. Lihat bell icon di kanan atas
3. Ada badge merah dengan jumlah notifikasi
4. Klik bell icon
5. Muncul notifikasi:
   
   📅 Pengumuman Hari Libur
   Hari libur: Hari Raya Idul Fitri pada tanggal 01 Apr 2025.
   Libur nasional
```

## 📱 Format Notifikasi

### Create Notification
```
Title: Pengumuman Hari Libur
Body: Hari libur: [Nama] pada tanggal [DD MMM YYYY]. [Keterangan]
Icon: heroicon-o-calendar
Color: warning (orange)
```

### Update Notification
```
Title: Update Hari Libur
Body: Hari libur [Nama] telah diperbarui. [Keterangan]
Icon: heroicon-o-calendar
Color: warning (orange)
```

## 🧪 Testing

### Test Script
```bash
php test-hari-libur-notification.php
```

Script akan:
1. Hitung jumlah siswa
2. Buat hari libur test
3. Cek notifikasi siswa
4. Update hari libur
5. Cek notifikasi lagi
6. Cleanup (hapus test data)

### Manual Test

#### Test 1: Create Hari Libur
```
1. Login sebagai admin@example.com
2. Buka /admin/hari-liburs
3. Klik "New Hari Libur"
4. Isi:
   - Nama: "Test Libur Nasional"
   - Tanggal: Besok
   - Keterangan: "Ini test notifikasi"
5. Klik "Create"
6. Logout
7. Login sebagai murid@example.com
8. Buka /student
9. Cek bell icon → Harus ada notifikasi baru
```

#### Test 2: Update Hari Libur
```
1. Login sebagai admin
2. Edit hari libur yang tadi dibuat
3. Ubah keterangan
4. Klik "Save"
5. Logout
6. Login sebagai murid
7. Cek bell icon → Harus ada notifikasi update
```

## 🔔 Notifikasi di Student Panel

Siswa akan melihat notifikasi di:
1. **Bell Icon**: Badge merah dengan jumlah unread
2. **Dropdown**: List notifikasi dengan icon calendar
3. **Auto-refresh**: Polling 30 detik (sudah dikonfigurasi)

## 📊 Target Notifikasi

Notifikasi dikirim ke:
- ✅ Semua user dengan role `murid`
- ✅ Termasuk siswa yang baru dibuat
- ✅ Tidak dikirim ke admin/guru (mereka yang buat)

## 🎯 Keuntungan

1. **Informasi Real-time**: Siswa langsung tahu ada hari libur
2. **Tidak Perlu Cek Manual**: Notifikasi otomatis muncul
3. **Transparan**: Semua siswa dapat info yang sama
4. **History**: Notifikasi tersimpan, bisa dilihat kapan saja

## 🔧 Konfigurasi

### Polling Interval
Student panel sudah dikonfigurasi dengan polling 30 detik:
```php
// app/Providers/Filament/StudentPanelProvider.php
->databaseNotificationsPolling('30s')
```

### Observer Registration
```php
// app/Providers/AppServiceProvider.php
HariLibur::observe(HariLiburObserver::class);
```

## 📝 Catatan

1. **Delete Event**: Tidak mengirim notifikasi untuk menghindari kebingungan
2. **Bulk Create**: Jika import banyak hari libur, setiap record akan trigger notifikasi
3. **Performance**: Untuk sekolah dengan banyak siswa (>1000), pertimbangkan queue
4. **Keterangan**: Jika tidak ada keterangan, hanya tampil nama dan tanggal

## 🚀 Next Steps (Opsional)

Jika ingin enhance:
1. **Queue**: Gunakan queue untuk kirim notifikasi async
2. **Filter**: Kirim hanya ke kelas tertentu
3. **Email**: Tambah channel email selain database
4. **Push**: Tambah push notification untuk mobile

## ✅ Status

- ✅ Notification class created
- ✅ Observer created
- ✅ Observer registered
- ✅ Test script created
- ✅ Documentation complete
- ✅ Ready to use!

Fitur sudah siap digunakan! Setiap kali admin/guru buat atau update hari libur, semua siswa akan otomatis dapat notifikasi.
