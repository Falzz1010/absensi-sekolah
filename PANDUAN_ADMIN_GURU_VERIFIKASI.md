# Panduan Monitoring Verifikasi Ganda untuk Admin & Guru

## 🎯 Overview

Sebagai Admin atau Guru, Anda dapat memonitor status verifikasi absensi siswa untuk memastikan semua siswa melakukan verifikasi ganda (QR Scan + Absensi Manual).

## 📊 Dashboard Widgets

### 1. Widget Status Verifikasi

Di halaman Dashboard, Anda akan melihat 5 statistik penting:

#### a. Total Absensi Hari Ini
- Jumlah total siswa yang hadir hari ini
- Termasuk semua status (Hadir, Terlambat)

#### b. Verifikasi Lengkap ✅
- Jumlah siswa yang sudah melakukan KEDUA metode (QR + Manual)
- Menampilkan tingkat kelengkapan dalam persentase
- **Target**: 100% siswa harus lengkap

#### c. Belum Lengkap ⚠️
- Jumlah siswa yang baru melakukan 1 metode
- Perlu tindak lanjut untuk melengkapi verifikasi
- **Warna kuning** = perlu perhatian

#### d. Hanya QR Scan
- Siswa yang sudah scan QR tapi belum absen manual
- Klik untuk melihat daftar lengkap

#### e. Hanya Manual
- Siswa yang sudah absen manual tapi belum scan QR
- Perlu diingatkan untuk scan QR

### 2. Tabel Verifikasi Belum Lengkap

Di bawah widget statistik, ada tabel yang menampilkan:
- **Nama Siswa**: Nama lengkap siswa
- **Kelas**: Kelas siswa
- **QR Scan**: Status QR scan (✓ Sudah / ✗ Belum)
- **Waktu QR**: Jam QR scan dilakukan
- **Manual**: Status absensi manual (✓ Sudah / ✗ Belum)
- **Waktu Manual**: Jam absensi manual dilakukan
- **Metode Pertama**: Metode yang digunakan pertama kali

**Fitur**:
- Auto-refresh setiap 30 detik
- Klik "Lihat Detail" untuk edit data
- Empty state jika semua sudah lengkap 🎉

## 📋 Menu Absensi

### Kolom Tambahan

Di menu **Absensi** → **Absensi**, Anda akan melihat kolom baru:

#### 1. Verifikasi
- Badge: **✅ Lengkap** (hijau) atau **⚠️ Belum Lengkap** (kuning)
- Description: Detail metode yang sudah/belum dilakukan
  - Contoh: `✓ QR (07:15) | ✓ Manual (07:20)`
  - Contoh: `✓ QR (07:15) | ✗ Manual`

#### 2. Waktu Check-in
- Menampilkan waktu check-in siswa
- Format: HH:mm (contoh: 07:15)

#### 3. Terlambat
- Badge menampilkan status keterlambatan
- Jika terlambat: "Ya (15 mnt)" dengan badge merah
- Jika tepat waktu: "Tidak" dengan badge hijau

### Filter Tambahan

#### Filter Status Verifikasi
Pilihan:
- **✅ Lengkap**: Hanya tampilkan siswa dengan verifikasi lengkap
- **⚠️ Belum Lengkap**: Hanya tampilkan siswa yang belum lengkap

#### Filter Belum Lengkap Hari Ini (Toggle)
- Aktifkan untuk melihat siswa yang belum lengkap hari ini
- Kombinasi: Hari ini + Status Hadir + Belum Lengkap
- Berguna untuk tindak lanjut cepat

## 🔔 Kirim Reminder

### Cara Kirim Reminder ke Siswa

1. **Buka Menu Absensi**
2. **Aktifkan Filter**: "Belum Lengkap Hari Ini"
3. **Pilih Siswa**: Centang siswa yang ingin dikirimi reminder
4. **Klik Bulk Action**: "Kirim Reminder"
5. **Konfirmasi**: Klik "Kirim Reminder" di modal

**Hasil**:
- Siswa akan menerima notifikasi di portal mereka
- Notifikasi berisi metode yang belum dilakukan
- Notifikasi tersimpan di database

**Contoh Notifikasi**:
```
⚠️ Reminder: Lengkapi Verifikasi Absensi

Anda belum melakukan Absensi Manual untuk tanggal 09/12/2025. 
Segera lengkapi verifikasi Anda!
```

## 📊 Monitoring & Reporting

### Cara Monitoring Efektif

#### Pagi Hari (07:00 - 08:00)
1. Cek widget "Total Absensi Hari Ini"
2. Monitor siswa yang mulai check-in
3. Perhatikan widget "Belum Lengkap"

#### Siang Hari (12:00)
1. Cek tabel "Verifikasi Belum Lengkap"
2. Identifikasi siswa yang belum lengkap
3. Kirim reminder via bulk action

#### Sore Hari (15:00)
1. Cek tingkat kelengkapan (target: 100%)
2. Follow up siswa yang masih belum lengkap
3. Hubungi wali kelas jika perlu

### Laporan Harian

**Metrik yang Perlu Dicatat**:
- Total siswa hadir
- Jumlah verifikasi lengkap
- Persentase kelengkapan
- Siswa yang belum lengkap (nama & kelas)
- Tindak lanjut yang dilakukan

## 🎯 Best Practices

### 1. Set Target Harian
- **Target**: 100% siswa dengan verifikasi lengkap
- **Toleransi**: Maksimal 5% belum lengkap (dengan alasan valid)

### 2. Reminder Berkala
- **Pagi**: Jam 10:00 (reminder pertama)
- **Siang**: Jam 13:00 (reminder kedua)
- **Sore**: Jam 15:00 (reminder terakhir)

### 3. Koordinasi dengan Wali Kelas
- Share data siswa yang sering belum lengkap
- Minta wali kelas untuk follow up
- Buat sistem reward untuk kelas dengan tingkat kelengkapan tertinggi

### 4. Analisis Pola
- Identifikasi siswa yang sering lupa
- Cari tahu alasan (masalah teknis, lupa, dll)
- Berikan solusi (reminder otomatis, edukasi, dll)

## ⚠️ Troubleshooting

### Issue: Siswa Claim Sudah Lengkap tapi Sistem Belum

**Solusi**:
1. Buka menu **Absensi**
2. Cari data siswa tersebut
3. Klik **Edit**
4. Cek field:
   - `qr_scan_done`
   - `manual_checkin_done`
   - `is_complete`
5. Jika ada yang salah, hubungi Admin IT

### Issue: Widget Tidak Update

**Solusi**:
1. Refresh halaman (F5)
2. Widget auto-refresh setiap 30 detik
3. Jika masih tidak update, clear cache browser

### Issue: Reminder Tidak Terkirim

**Solusi**:
1. Pastikan siswa memiliki akun user
2. Cek koneksi database
3. Cek log error di sistem

## 📱 Akses Mobile

Dashboard dan monitoring dapat diakses via mobile:
- Buka browser di HP
- Login ke `/admin`
- Semua widget responsive
- Tabel dapat di-scroll horizontal

## 🔐 Permissions

### Admin
- ✅ Lihat semua data
- ✅ Edit data absensi
- ✅ Kirim reminder
- ✅ Akses semua filter
- ✅ Export laporan

### Guru
- ✅ Lihat data kelas yang diajar
- ✅ Edit data absensi kelas sendiri
- ✅ Kirim reminder ke siswa kelas sendiri
- ⚠️ Tidak bisa edit data kelas lain

## 📊 Export Data

### Export Absensi dengan Status Verifikasi

1. Buka menu **Absensi**
2. Set filter sesuai kebutuhan
3. Klik **Export** (jika tersedia)
4. Pilih format: Excel atau PDF

**Data yang Ter-export**:
- Nama siswa
- Kelas
- Tanggal
- Status
- Status verifikasi (Lengkap/Belum)
- Detail metode (QR/Manual)
- Waktu check-in

## 🎓 Tips & Trik

### Tip 1: Gunakan Filter Kombinasi
Contoh: Filter "Hari Ini" + "Belum Lengkap" + "Kelas 12 IPA"
→ Lihat siswa kelas 12 IPA yang belum lengkap hari ini

### Tip 2: Bookmark URL Filter
Setelah set filter, bookmark URL-nya untuk akses cepat besok

### Tip 3: Screenshot Widget
Screenshot widget statistik setiap hari untuk dokumentasi

### Tip 4: Set Reminder di HP
Set alarm di HP untuk cek dashboard di jam-jam krusial

### Tip 5: Koordinasi dengan Piket
Minta guru piket untuk monitor dashboard dan kirim reminder

## 📞 Kontak Support

Jika ada masalah teknis:
- **Admin IT**: it@sekolah.com
- **Telp**: 021-xxx-xxxx
- **WhatsApp**: 08xx-xxxx-xxxx

## 📈 Metrik Keberhasilan

### Indikator Sistem Berjalan Baik

✅ **Tingkat kelengkapan > 95%** setiap hari
✅ **Waktu rata-rata melengkapi < 2 jam** (dari metode pertama ke kedua)
✅ **Jumlah reminder < 10%** dari total siswa
✅ **Tidak ada siswa yang sama berulang kali belum lengkap**

### Indikator Perlu Perbaikan

⚠️ **Tingkat kelengkapan < 80%**
⚠️ **Banyak siswa yang hanya QR atau hanya Manual**
⚠️ **Siswa yang sama berulang kali belum lengkap**
⚠️ **Banyak komplain dari siswa tentang sistem**

## 🎯 Action Plan Jika Tingkat Kelengkapan Rendah

### Minggu 1: Edukasi
- Sosialisasi sistem ke semua siswa
- Buat poster/banner tentang verifikasi ganda
- Share panduan di grup WhatsApp

### Minggu 2: Monitoring Ketat
- Cek dashboard setiap jam
- Kirim reminder berkala
- Follow up siswa yang belum lengkap

### Minggu 3: Reward & Punishment
- Beri reward kelas dengan tingkat kelengkapan tertinggi
- Beri sanksi ringan untuk siswa yang sering tidak lengkap

### Minggu 4: Evaluasi
- Analisis data 1 bulan
- Identifikasi masalah utama
- Buat perbaikan sistem jika perlu

---

## ✅ Checklist Harian untuk Admin/Guru

### Pagi (07:00 - 08:00)
- [ ] Login ke dashboard
- [ ] Cek widget "Total Absensi Hari Ini"
- [ ] Monitor siswa yang mulai check-in

### Siang (12:00 - 13:00)
- [ ] Cek tabel "Verifikasi Belum Lengkap"
- [ ] Kirim reminder ke siswa yang belum lengkap
- [ ] Catat jumlah siswa belum lengkap

### Sore (15:00 - 16:00)
- [ ] Cek tingkat kelengkapan akhir
- [ ] Follow up siswa yang masih belum lengkap
- [ ] Buat laporan harian
- [ ] Share ke wali kelas jika ada siswa bermasalah

---

**Dibuat**: 9 Desember 2025
**Versi**: 1.0.0
**Untuk**: Admin & Guru
