# Fitur Absen Manual untuk Siswa

## ⚠️ PENTING: Sistem Double Verification

**WAJIB 2 ABSENSI**: Siswa harus melakukan **QR Scan** DAN **Absensi Manual** untuk dianggap hadir secara sah. Urutan bebas (bisa QR dulu atau Manual dulu).

Lihat dokumentasi lengkap: `SISTEM_DOUBLE_VERIFICATION.md`

## Overview

Fitur absen manual adalah **bagian dari sistem verifikasi ganda** untuk mencegah kecurangan. Siswa WAJIB melakukan absensi manual sebagai konfirmasi tambahan setelah/sebelum QR Scan.

## Fitur Utama

### 1. Form Absensi Manual
- **Tanggal**: Pilih tanggal kehadiran (maksimal hari ini)
- **Waktu Check-in**: Input waktu kehadiran yang sebenarnya
- **Catatan**: Opsional, untuk menambahkan keterangan tambahan

### 2. Deteksi Keterlambatan Otomatis
- Sistem otomatis menghitung keterlambatan berdasarkan jam pelajaran pertama
- Menampilkan durasi keterlambatan dalam menit
- Status keterlambatan tersimpan di database

### 3. Validasi
- ✅ Mencegah duplikasi absensi (1 absensi per hari)
- ✅ Tidak dapat absen untuk tanggal yang akan datang
- ✅ Validasi data siswa (harus memiliki record Murid)
- ✅ Waktu check-in harus valid

### 4. UI/UX
- Info card dengan instruksi penggunaan
- Quick stats menampilkan status, tanggal, dan waktu saat ini
- Help section dengan panduan lengkap
- Alert system terintegrasi untuk notifikasi
- Responsive design untuk mobile dan desktop

## File yang Dibuat

### 1. Controller/Page
```
app/Filament/Student/Pages/ManualAttendancePage.php
```
- Handle form submission
- Validasi data
- Deteksi keterlambatan
- Create attendance record
- Send notifications

### 2. View
```
resources/views/filament/student/pages/manual-attendance-page.blade.php
```
- Beautiful UI dengan Tailwind CSS
- Info cards dan help section
- Quick stats widgets
- Responsive layout

## Cara Menggunakan

### Untuk Siswa:

1. **Login ke Portal Siswa** (`/student`)
2. **Klik menu "Absen Manual"** di sidebar
3. **Isi formulir**:
   - Pilih tanggal (default: hari ini)
   - Pilih waktu check-in (default: waktu saat ini)
   - Tambahkan catatan jika perlu (opsional)
4. **Klik "Simpan Absensi"**
5. **Sistem akan**:
   - Validasi data
   - Cek duplikasi
   - Deteksi keterlambatan
   - Simpan ke database
   - Tampilkan notifikasi sukses/error

### Contoh Notifikasi:

**Tepat Waktu:**
```
✅ Berhasil!
Absensi berhasil dicatat. Anda hadir tepat waktu!
```

**Terlambat:**
```
✅ Berhasil!
Absensi berhasil dicatat. Anda terlambat 15 menit.
```

**Duplikasi:**
```
⚠️ Gagal
Anda sudah melakukan absensi untuk tanggal ini
```

## Integrasi dengan Sistem

### 1. Database
Data tersimpan di tabel `absensis` dengan field:
- `murid_id`: ID siswa
- `tanggal`: Tanggal kehadiran
- `status`: "Hadir" (fixed untuk manual attendance)
- `kelas`: Kelas siswa
- `keterangan`: Catatan dari siswa
- `check_in_time`: Waktu check-in
- `is_late`: Boolean, true jika terlambat
- `late_duration`: Durasi keterlambatan dalam menit

### 2. Real-time Notifications
- Menggunakan alert system yang sudah ada
- Notifikasi sukses/error/warning
- Auto-dismiss dengan countdown

### 3. Navigation
Menu "Absen Manual" muncul di Student Panel dengan:
- Icon: `heroicon-o-hand-raised`
- Sort order: 2 (setelah Dashboard, sebelum Ajukan Izin/Sakit)
- Label: "Absen Manual"

## Perbedaan dengan Fitur Lain

| Fitur | Absen Manual | QR Scanner | Ajukan Izin/Sakit |
|-------|--------------|------------|-------------------|
| **Status** | Hadir saja | Hadir saja | Sakit/Izin saja |
| **Bukti** | Tidak perlu | QR Code | Dokumen wajib |
| **Waktu** | Input manual | Otomatis | Pilih tanggal |
| **Verifikasi** | **WAJIB + QR Scan** | **WAJIB + Manual** | Perlu verifikasi admin |
| **Use Case** | **Part of double verification** | **Part of double verification** | Tidak hadir |
| **Complete Status** | ✅ Jika QR Scan juga done | ✅ Jika Manual juga done | N/A |

## Keamanan

1. **Authorization**: Hanya user dengan role `murid` yang dapat akses
2. **Validation**: 
   - Cek user memiliki record Murid
   - Cek duplikasi absensi
   - Cek tanggal tidak di masa depan
3. **Data Integrity**: 
   - Foreign key constraint ke tabel murids
   - Cascade delete jika murid dihapus

## Testing

### Manual Testing Checklist:

- [ ] Login sebagai siswa
- [ ] Akses menu "Absen Manual"
- [ ] Submit absensi dengan waktu tepat waktu
- [ ] Cek notifikasi sukses
- [ ] Coba submit lagi untuk hari yang sama (harus gagal)
- [ ] Submit absensi dengan waktu terlambat
- [ ] Cek durasi keterlambatan di notifikasi
- [ ] Cek data tersimpan di database
- [ ] Cek tampilan di "Riwayat Kehadiran"
- [ ] Test responsive di mobile

### Expected Results:

1. ✅ Absensi tersimpan dengan benar
2. ✅ Keterlambatan terdeteksi otomatis
3. ✅ Notifikasi muncul dengan pesan yang sesuai
4. ✅ Duplikasi dicegah
5. ✅ UI responsive di semua device

## Troubleshooting

### Issue: "Data siswa tidak ditemukan"
**Solusi**: 
- Pastikan user memiliki record di tabel `murids`
- Cek `user_id` di tabel murids sesuai dengan user yang login

### Issue: "Anda sudah melakukan absensi untuk tanggal ini"
**Solusi**: 
- Ini adalah validasi normal
- Siswa hanya boleh absen 1x per hari
- Jika perlu update, admin harus edit dari panel admin

### Issue: Keterlambatan tidak terdeteksi
**Solusi**: 
- Pastikan ada data di tabel `jam_pelajarans`
- Cek field `is_active = true` dan `urutan` terkecil
- Jam pelajaran pertama digunakan sebagai acuan

## Future Enhancements

Fitur yang bisa ditambahkan di masa depan:

1. **Geolocation Validation**: Cek lokasi siswa saat absen
2. **Photo Capture**: Ambil foto selfie saat absen manual
3. **Reason Selection**: Dropdown alasan kenapa tidak bisa scan QR
4. **Admin Approval**: Absen manual perlu approval admin
5. **Time Limit**: Batasi waktu absen manual (misal: hanya sampai jam 10 pagi)
6. **Statistics**: Dashboard untuk melihat perbandingan QR vs Manual attendance

## Status

✅ **COMPLETED**

- [x] Create ManualAttendancePage.php
- [x] Create manual-attendance-page.blade.php
- [x] Form validation
- [x] Late detection logic
- [x] Alert system integration
- [x] Beautiful UI with Tailwind
- [x] Help section and instructions
- [x] Documentation

## Navigation Menu

Menu akan otomatis muncul di Student Panel dengan urutan:
1. Dashboard (sort: 1)
2. **Absen Manual** (sort: 2) ← NEW
3. Scan QR (sort: 2)
4. Ajukan Izin/Sakit (sort: 3)
5. Riwayat Kehadiran (sort: 4)
6. Profil Saya (sort: 5)

---

**Created**: December 9, 2025
**Version**: 1.0.0
**Status**: Production Ready
