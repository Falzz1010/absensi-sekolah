# Fitur Pengajuan Izin/Sakit untuk Admin & Guru

## Overview

Fitur ini memungkinkan Admin dan Guru untuk **melihat, memverifikasi, dan mengelola** pengajuan izin/sakit yang diajukan oleh siswa melalui Student Portal.

## Status Implementasi

✅ **SUDAH ADA** - Fitur pengajuan izin/sakit untuk Admin & Guru sudah berhasil dibuat!

### Yang Sudah Ada

1. ✅ **Student Panel** - Siswa bisa ajukan izin/sakit
   - Form pengajuan dengan upload bukti
   - Status: Sakit atau Izin
   - Upload foto surat dokter/izin orang tua

2. ✅ **Admin/Guru Panel** - Verifikasi pengajuan (BARU!)
   - Resource "Pengajuan Izin/Sakit"
   - Lihat semua pengajuan
   - Setujui/Tolak pengajuan
   - Bulk approval
   - Badge notifikasi untuk pending

## Fitur Admin/Guru Panel

### 1. Menu Navigation
- **Lokasi**: Manajemen Absensi > Pengajuan Izin/Sakit
- **Icon**: Document Check
- **Badge**: Menampilkan jumlah pengajuan yang menunggu verifikasi

### 2. Tabs Filter
- **Semua**: Semua pengajuan
- **Menunggu Verifikasi**: Status pending (badge kuning)
- **Disetujui**: Status approved (badge hijau)
- **Ditolak**: Status rejected (badge merah)

### 3. Tabel Pengajuan

**Kolom**:
- Tanggal
- Nama Siswa
- Kelas
- Jenis (Sakit/Izin)
- Alasan (dengan tooltip jika panjang)
- Bukti (thumbnail gambar)
- Status Verifikasi
- Diajukan (timestamp)

**Filter**:
- Filter by Jenis (Sakit/Izin)
- Filter by Status Verifikasi
- Filter by Tanggal (dari-sampai)

### 4. Actions

**Per Record**:
- **View**: Lihat detail lengkap
- **Edit**: Edit status verifikasi dan catatan
- **Setujui**: Quick approve (hanya untuk pending)
- **Tolak**: Quick reject dengan alasan (hanya untuk pending)

**Bulk Actions**:
- **Setujui Terpilih**: Approve multiple records sekaligus
- **Delete**: Hapus multiple records

### 5. Form Detail

**Section 1: Informasi Siswa**
- Nama Siswa (disabled)
- Kelas (disabled)

**Section 2: Detail Pengajuan**
- Tanggal (disabled)
- Jenis: Sakit/Izin (disabled)
- Alasan (disabled, read-only)
- Bukti Pendukung (view/download)

**Section 3: Verifikasi**
- Status Verifikasi (dropdown: Pending/Disetujui/Ditolak)
- Catatan Verifikasi (textarea)

## Workflow

### Dari Sisi Siswa
1. Login ke Student Panel
2. Klik "Ajukan Izin/Sakit"
3. Isi form:
   - Pilih jenis (Sakit/Izin)
   - Pilih tanggal
   - Tulis alasan
   - Upload bukti (foto surat)
4. Submit
5. Status: **Menunggu Verifikasi**

### Dari Sisi Admin/Guru
1. Login ke Admin Panel
2. Lihat badge notifikasi di menu "Pengajuan Izin/Sakit"
3. Buka menu tersebut
4. Tab "Menunggu Verifikasi" otomatis aktif
5. Lihat detail pengajuan:
   - Baca alasan
   - Lihat bukti pendukung
6. **Pilihan 1: Quick Action**
   - Klik tombol "Setujui" (hijau) atau "Tolak" (merah)
   - Jika tolak, isi alasan penolakan
7. **Pilihan 2: Edit Form**
   - Klik "Edit"
   - Ubah status verifikasi
   - Tambahkan catatan
   - Save
8. Status berubah menjadi **Disetujui** atau **Ditolak**

### Bulk Approval
1. Centang multiple pengajuan
2. Klik "Setujui Terpilih"
3. Konfirmasi
4. Semua pengajuan terpilih disetujui sekaligus

## Database Schema

### Tabel: `absensis`

**Fields untuk Pengajuan Izin/Sakit**:
```sql
proof_document VARCHAR(255) NULL          -- Path file bukti
verification_status ENUM NULL             -- pending, approved, rejected
verified_by BIGINT UNSIGNED NULL          -- Foreign key ke users
verified_at TIMESTAMP NULL                -- Waktu verifikasi
verification_notes TEXT NULL              -- Catatan verifikasi (BARU!)
```

**Query untuk Filter**:
```sql
-- Hanya tampilkan pengajuan izin/sakit
WHERE verification_status IS NOT NULL
  AND status IN ('Sakit', 'Izin')

-- Filter pending
WHERE verification_status = 'pending'

-- Filter approved
WHERE verification_status = 'approved'

-- Filter rejected
WHERE verification_status = 'rejected'
```

## Files yang Dibuat

### 1. Resource
- `app/Filament/Resources/PengajuanIzinResource.php`

### 2. Pages
- `app/Filament/Resources/PengajuanIzinResource/Pages/ListPengajuanIzins.php`
- `app/Filament/Resources/PengajuanIzinResource/Pages/ViewPengajuanIzin.php`
- `app/Filament/Resources/PengajuanIzinResource/Pages/EditPengajuanIzin.php`

### 3. Migration
- `database/migrations/2025_12_09_061520_add_verification_notes_to_absensis_table.php`

### 4. Model Update
- `app/Models/Absensi.php` - Added `verification_notes` to fillable

## Cara Test

### Test 1: Lihat Pengajuan
```bash
1. Login sebagai Admin/Guru
2. Buka menu "Pengajuan Izin/Sakit"
3. Cek badge notifikasi (jumlah pending)
4. Lihat tab "Menunggu Verifikasi"
```

### Test 2: Approve Pengajuan
```bash
1. Pilih satu pengajuan pending
2. Klik tombol "Setujui" (hijau)
3. Konfirmasi
4. Cek status berubah jadi "Disetujui"
5. Cek tab "Disetujui" bertambah
```

### Test 3: Reject Pengajuan
```bash
1. Pilih satu pengajuan pending
2. Klik tombol "Tolak" (merah)
3. Isi alasan penolakan
4. Konfirmasi
5. Cek status berubah jadi "Ditolak"
6. Cek catatan verifikasi tersimpan
```

### Test 4: Bulk Approval
```bash
1. Centang 3-5 pengajuan pending
2. Klik "Setujui Terpilih"
3. Konfirmasi
4. Cek semua status berubah jadi "Disetujui"
```

### Test 5: View Detail
```bash
1. Klik "View" pada satu pengajuan
2. Cek semua informasi tampil:
   - Nama siswa & kelas
   - Tanggal & jenis
   - Alasan lengkap
   - Bukti pendukung (bisa download)
   - Status verifikasi
```

## Notifikasi Badge

Badge di menu navigation menampilkan jumlah pengajuan yang **menunggu verifikasi**:
- Warna: Kuning (warning)
- Update otomatis saat ada pengajuan baru
- Hilang jika tidak ada pending

## Permissions

Resource ini accessible untuk:
- ✅ Admin (full access)
- ✅ Guru (full access)
- ❌ Murid (tidak bisa akses)

## Tips Penggunaan

### Untuk Admin/Guru
1. **Cek Bukti Pendukung**: Selalu lihat bukti sebelum approve
2. **Berikan Catatan**: Tambahkan catatan saat reject untuk transparansi
3. **Bulk Approval**: Gunakan untuk approve banyak pengajuan sekaligus
4. **Filter Tanggal**: Gunakan filter untuk cari pengajuan spesifik

### Best Practices
1. Verifikasi pengajuan maksimal 1x24 jam
2. Selalu cek keaslian bukti pendukung
3. Berikan alasan jelas saat menolak
4. Gunakan bulk approval untuk efisiensi

## Integrasi dengan Student Portal

### Student View
Siswa bisa lihat status pengajuan mereka di:
- **Riwayat Absensi**: Badge status verifikasi
- **Widget Summary**: Jumlah menunggu verifikasi
- **Notifications**: Notifikasi saat disetujui/ditolak (future)

### Status Badge Colors
- **Pending**: Kuning (warning)
- **Approved**: Hijau (success)
- **Rejected**: Merah (danger)

## Future Enhancements

Fitur yang bisa ditambahkan:
1. ✨ Notifikasi real-time ke siswa saat disetujui/ditolak
2. ✨ Export laporan pengajuan izin/sakit
3. ✨ Statistik pengajuan per kelas/bulan
4. ✨ Auto-reject jika bukti tidak valid
5. ✨ Reminder untuk guru jika ada pending > 24 jam

## Kesimpulan

✅ **Fitur Pengajuan Izin/Sakit untuk Admin & Guru sudah LENGKAP!**

**Fitur Utama**:
- Lihat semua pengajuan dari siswa
- Filter by status, jenis, tanggal
- Approve/Reject dengan catatan
- Bulk approval
- Badge notifikasi
- View bukti pendukung

**Ready to Use**: Silakan test dan gunakan fitur ini untuk mengelola pengajuan izin/sakit siswa!
