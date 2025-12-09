# ✅ Fitur Pengumuman Penting untuk Siswa

## 🎯 Overview

Fitur untuk admin/guru membuat pengumuman penting yang akan otomatis mengirim notifikasi ke siswa. Berbeda dengan Hari Libur, pengumuman ini lebih fleksibel dan bisa ditargetkan ke kelas tertentu.

## 📋 Fitur Utama

### 1. Prioritas Pengumuman
- **Tinggi** (Merah): Pengumuman urgent/penting
- **Sedang** (Kuning): Pengumuman biasa
- **Rendah** (Biru): Pengumuman informasi

### 2. Target Penerima
- **Semua Siswa**: Notifikasi dikirim ke seluruh siswa
- **Kelas Tertentu**: Notifikasi hanya untuk kelas yang dipilih

### 3. Rich Text Editor
- Format teks (bold, italic, underline)
- Bullet points dan numbering
- Link dan formatting lainnya

### 4. Status Aktif/Nonaktif
- Pengumuman bisa dinonaktifkan tanpa dihapus
- Hanya pengumuman aktif yang kirim notifikasi

### 5. Tanggal Publikasi
- Bisa dijadwalkan untuk masa depan
- Notifikasi dikirim sesuai tanggal publikasi

## 🗂️ Struktur Database

**Table**: `pengumumen`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| judul | string | Judul pengumuman |
| isi | text | Isi pengumuman (HTML) |
| prioritas | enum | rendah, sedang, tinggi |
| target | enum | semua, kelas_tertentu |
| kelas_target | string | Comma-separated kelas names |
| created_by | foreignId | User yang membuat |
| is_active | boolean | Status aktif |
| published_at | timestamp | Tanggal publikasi |
| created_at | timestamp | - |
| updated_at | timestamp | - |

## 📱 Komponen yang Dibuat

### 1. Model
**File**: `app/Models/Pengumuman.php`
- Fillable fields
- Relationship dengan User (creator)
- Helper method `getTargetKelasArray()`

### 2. Migration
**File**: `database/migrations/2025_12_09_070507_create_pengumumen_table.php`
- Schema lengkap dengan semua field

### 3. Resource
**File**: `app/Filament/Resources/PengumumanResource.php`
- Form dengan RichEditor
- Table dengan polling 30s
- Filters (prioritas, target, status)
- Badge colors sesuai prioritas

### 4. Notification
**File**: `app/Notifications/PengumumanNotification.php`
- Format notifikasi dengan icon dan color sesuai prioritas
- Strip HTML tags untuk preview
- Limit 150 karakter

### 5. Observer
**File**: `app/Observers/PengumumanObserver.php`
- Handle created event
- Handle updated event
- Logic untuk target semua/kelas tertentu
- Check active status dan published date

## 🎬 Cara Penggunaan

### Skenario 1: Pengumuman untuk Semua Siswa
```
1. Login sebagai admin/guru
2. Buka menu "Pengumuman"
3. Klik "New Pengumuman"
4. Isi form:
   - Judul: "Ujian Tengah Semester"
   - Isi: "Ujian akan dilaksanakan tanggal 15-20 Des 2025"
   - Prioritas: Tinggi
   - Target: Semua Siswa
   - Status: Aktif
   - Tanggal Publikasi: Sekarang
5. Klik "Create"

→ OTOMATIS: Semua siswa dapat notifikasi
```

### Skenario 2: Pengumuman untuk Kelas Tertentu
```
1. Login sebagai admin/guru
2. Buka menu "Pengumuman"
3. Klik "New Pengumuman"
4. Isi form:
   - Judul: "Kunjungan Industri"
   - Isi: "Kunjungan industri untuk kelas X IPA 1"
   - Prioritas: Sedang
   - Target: Kelas Tertentu
   - Pilih Kelas: X IPA 1, X IPA 2
   - Status: Aktif
5. Klik "Create"

→ OTOMATIS: Hanya siswa X IPA 1 dan X IPA 2 yang dapat notifikasi
```

### Skenario 3: Jadwalkan Pengumuman
```
1. Buat pengumuman seperti biasa
2. Set Tanggal Publikasi: Besok jam 08:00
3. Klik "Create"

→ Pengumuman tersimpan tapi notifikasi belum dikirim
→ Notifikasi akan dikirim otomatis besok jam 08:00
```

## 📊 Format Notifikasi

### Prioritas Tinggi (Danger)
```
🚨 Ujian Tengah Semester
Ujian akan dilaksanakan pada tanggal 15-20 Desember 2025...
Icon: heroicon-o-exclamation-triangle
Color: Red
```

### Prioritas Sedang (Warning)
```
📢 Kunjungan Industri
Kunjungan industri untuk kelas X IPA 1 akan dilaksanakan...
Icon: heroicon-o-megaphone
Color: Orange
```

### Prioritas Rendah (Info)
```
ℹ️ Informasi Perpustakaan
Perpustakaan akan tutup pada hari Sabtu...
Icon: heroicon-o-information-circle
Color: Blue
```

## 🧪 Testing

### Test Script
```bash
php test-pengumuman-notification.php
```

Script akan:
1. Hitung jumlah siswa
2. Buat pengumuman untuk semua siswa
3. Cek notifikasi siswa
4. Buat pengumuman untuk kelas tertentu
5. Cek notifikasi siswa kelas tersebut
6. Cleanup test data

### Manual Test

#### Test 1: Pengumuman Semua Siswa
```
1. Login sebagai admin
2. Buka /admin/pengumumen
3. Klik "New Pengumuman"
4. Isi:
   - Judul: "Test Pengumuman"
   - Isi: "Ini test pengumuman untuk semua siswa"
   - Prioritas: Tinggi
   - Target: Semua Siswa
5. Klik "Create"
6. Logout, login sebagai murid
7. Cek bell icon → Harus ada notifikasi
```

#### Test 2: Pengumuman Kelas Tertentu
```
1. Login sebagai admin
2. Buat pengumuman dengan target "Kelas Tertentu"
3. Pilih kelas X IPA 1
4. Klik "Create"
5. Login sebagai siswa X IPA 1 → Ada notifikasi
6. Login sebagai siswa kelas lain → Tidak ada notifikasi
```

## 🔔 Notifikasi di Student Panel

Siswa akan melihat:
1. **Bell Icon**: Badge merah dengan jumlah unread
2. **Dropdown**: List notifikasi dengan icon sesuai prioritas
3. **Auto-refresh**: Polling 30 detik
4. **Color Coding**: Merah (tinggi), Kuning (sedang), Biru (rendah)

## 📝 Perbedaan dengan Hari Libur

| Fitur | Hari Libur | Pengumuman |
|-------|------------|------------|
| Target | Semua siswa | Semua/Kelas tertentu |
| Prioritas | - | Rendah/Sedang/Tinggi |
| Format | Text biasa | Rich text (HTML) |
| Icon | Calendar | Megaphone/Warning/Info |
| Use Case | Libur nasional | Ujian, acara, info umum |

## 🎯 Use Cases

### Prioritas Tinggi
- Ujian mendadak
- Perubahan jadwal penting
- Pengumuman darurat
- Pembatalan kegiatan

### Prioritas Sedang
- Kunjungan industri
- Acara sekolah
- Pengumpulan tugas
- Rapat kelas

### Prioritas Rendah
- Info perpustakaan
- Pengumuman umum
- Reminder biasa
- Info ekstrakurikuler

## ⚙️ Konfigurasi

### Polling Interval
```php
// PengumumanResource.php
->poll('30s')
```

### Observer Registration
```php
// AppServiceProvider.php
Pengumuman::observe(PengumumanObserver::class);
```

### Navigation
```php
protected static ?string $navigationGroup = 'Pengaturan';
protected static ?int $navigationSort = 5;
```

## 🚀 Fitur Lanjutan (Opsional)

Jika ingin enhance:
1. **Attachment**: Upload file PDF/gambar
2. **Read Receipt**: Track siapa yang sudah baca
3. **Comments**: Siswa bisa comment
4. **Archive**: Auto-archive pengumuman lama
5. **Email**: Kirim juga via email
6. **Push Notification**: Mobile push
7. **Schedule**: Cron job untuk scheduled announcements

## ✅ Status

- ✅ Model & Migration created
- ✅ Resource with rich editor
- ✅ Notification class
- ✅ Observer with target logic
- ✅ Observer registered
- ✅ Test script created
- ✅ Documentation complete
- ✅ Ready to use!

## 📖 Cara Akses

**Admin/Guru Panel:**
- URL: `/admin/pengumumen`
- Menu: Pengaturan → Pengumuman

**Student Panel:**
- Notifikasi muncul di bell icon
- Auto-refresh setiap 30 detik

Fitur sudah siap digunakan! Admin/guru bisa langsung membuat pengumuman dan siswa akan otomatis menerima notifikasi.
