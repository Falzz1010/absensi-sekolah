# 👨‍🏫 Tutorial Monitoring Verifikasi - Admin & Guru

## 🎯 Tujuan

Memastikan semua siswa melakukan **verifikasi ganda** (QR Scan + Absensi Manual) setiap hari.

---

## 📊 Dashboard - Lihat Sekilas

### Langkah 1: Login
```
1. Buka browser
2. Ketik: sekolah.com/admin
3. Login dengan username & password
4. Otomatis masuk ke Dashboard
```

### Langkah 2: Lihat Widget Verifikasi

Di bagian atas dashboard, ada **5 statistik penting**:

#### 1️⃣ Total Absensi Hari Ini
```
Contoh: 150 siswa
= Jumlah siswa yang hadir hari ini
```

#### 2️⃣ Verifikasi Lengkap ✅
```
Contoh: 120 siswa (80%)
= Siswa yang sudah QR Scan + Manual
= TARGET: Harus 100%!
```

#### 3️⃣ Belum Lengkap ⚠️
```
Contoh: 30 siswa
= Siswa yang baru 1 metode
= PERLU TINDAK LANJUT!
```

#### 4️⃣ Hanya QR Scan
```
Contoh: 20 siswa
= Sudah scan QR, belum manual
= Klik untuk lihat daftar
```

#### 5️⃣ Hanya Manual
```
Contoh: 10 siswa
= Sudah manual, belum scan QR
= Perlu diingatkan
```

---

## 📋 Tabel Siswa Belum Lengkap

### Di Bawah Widget

Ada tabel yang menampilkan siswa yang **belum lengkap**:

| Nama | Kelas | QR Scan | Waktu QR | Manual | Waktu Manual | Metode Pertama |
|------|-------|---------|----------|--------|--------------|----------------|
| Budi | 12 IPA | ✓ Sudah | 07:15 | ✗ Belum | - | QR Scan |
| Ani | 11 IPS | ✗ Belum | - | ✓ Sudah | 07:20 | Manual |

**Fitur**:
- Auto-refresh setiap 30 detik
- Klik "Lihat Detail" untuk edit
- Kosong = Semua sudah lengkap 🎉

---

## 🔍 Menu Absensi - Detail Lengkap

### Langkah 1: Buka Menu Absensi
```
1. Klik menu "Absensi" di sidebar
2. Klik "Absensi"
3. Lihat tabel lengkap
```

### Langkah 2: Lihat Kolom Verifikasi

Ada **3 kolom baru**:

#### Kolom "Verifikasi"
```
✅ Lengkap (hijau)
= Kedua metode sudah dilakukan
= Detail: ✓ QR (07:15) | ✓ Manual (07:20)

⚠️ Belum Lengkap (kuning)
= Baru 1 metode
= Detail: ✓ QR (07:15) | ✗ Manual
```

#### Kolom "Waktu Check-in"
```
Contoh: 07:15
= Jam siswa check-in
```

#### Kolom "Terlambat"
```
Ya (15 mnt) = Terlambat 15 menit (merah)
Tidak = Tepat waktu (hijau)
```

---

## 🔎 Filter - Cari Cepat

### Filter 1: Status Verifikasi
```
1. Klik dropdown "Status Verifikasi"
2. Pilih:
   - ✅ Lengkap = Hanya yang sudah lengkap
   - ⚠️ Belum Lengkap = Hanya yang belum lengkap
```

### Filter 2: Belum Lengkap Hari Ini (Toggle)
```
1. Aktifkan toggle "Belum Lengkap Hari Ini"
2. Otomatis filter:
   - Hari ini
   - Status Hadir
   - Belum lengkap
3. Langsung lihat siswa yang perlu ditindak lanjut!
```

### Filter 3: Kombinasi
```
Contoh:
- Filter Kelas: 12 IPA
- Filter Status Verifikasi: Belum Lengkap
- Toggle Hari Ini: ON

Hasil: Siswa kelas 12 IPA yang belum lengkap hari ini
```

---

## 🔔 Kirim Reminder - Ingatkan Siswa

### Langkah-langkah:

#### 1. Filter Siswa Belum Lengkap
```
1. Buka menu "Absensi"
2. Aktifkan toggle "Belum Lengkap Hari Ini"
3. Lihat daftar siswa
```

#### 2. Pilih Siswa
```
1. Centang siswa yang ingin dikirimi reminder
2. Atau centang semua (checkbox di header)
```

#### 3. Kirim Reminder
```
1. Klik dropdown "Bulk Actions"
2. Pilih "Kirim Reminder"
3. Muncul modal konfirmasi
4. Klik "Kirim Reminder"
```

#### 4. Hasil
```
✅ Notifikasi terkirim!
Contoh: "Berhasil mengirim 15 reminder ke siswa"

Siswa akan menerima notifikasi:
"⚠️ Reminder: Lengkapi Verifikasi Absensi
Anda belum melakukan [Metode] untuk tanggal 09/12/2025.
Segera lengkapi verifikasi Anda!"
```

---

## ⏰ Rutinitas Harian

### Pagi (07:00 - 08:00)
```
✅ Login ke dashboard
✅ Cek widget "Total Absensi Hari Ini"
✅ Monitor siswa yang mulai check-in
✅ Perhatikan widget "Belum Lengkap"
```

### Siang (12:00)
```
✅ Cek tabel "Verifikasi Belum Lengkap"
✅ Identifikasi siswa yang belum lengkap
✅ Kirim reminder via bulk action
✅ Catat jumlah siswa belum lengkap
```

### Sore (15:00)
```
✅ Cek tingkat kelengkapan (target: 100%)
✅ Follow up siswa yang masih belum lengkap
✅ Hubungi wali kelas jika perlu
✅ Buat laporan harian
```

---

## 📊 Target & Metrik

### Target Harian
```
🎯 Tingkat Kelengkapan: > 95%
🎯 Waktu Melengkapi: < 2 jam
🎯 Reminder Sent: < 10%
```

### Indikator Baik ✅
```
✅ Tingkat kelengkapan > 95%
✅ Sedikit reminder (< 10%)
✅ Tidak ada siswa yang sama berulang kali
```

### Indikator Perlu Perbaikan ⚠️
```
⚠️ Tingkat kelengkapan < 80%
⚠️ Banyak reminder (> 30%)
⚠️ Siswa yang sama berulang kali belum lengkap
```

---

## 🎯 Tips Monitoring Efektif

### Tip 1: Gunakan Filter Kombinasi
```
Contoh:
Filter "Hari Ini" + "Belum Lengkap" + "Kelas 12 IPA"
= Lihat siswa kelas 12 IPA yang belum lengkap hari ini
```

### Tip 2: Bookmark URL Filter
```
Setelah set filter, bookmark URL-nya
Besok tinggal klik bookmark untuk akses cepat
```

### Tip 3: Screenshot Widget
```
Screenshot widget statistik setiap hari
Untuk dokumentasi dan laporan
```

### Tip 4: Set Reminder di HP
```
Set alarm di HP untuk cek dashboard:
- 07:30 (pagi)
- 12:00 (siang)
- 15:00 (sore)
```

### Tip 5: Koordinasi dengan Wali Kelas
```
Share data siswa yang sering belum lengkap
Minta wali kelas untuk follow up
Buat sistem reward untuk kelas terbaik
```

---

## 🚨 Troubleshooting

### Widget Tidak Update
```
Solusi:
1. Refresh halaman (F5)
2. Widget auto-refresh setiap 30 detik
3. Clear cache browser jika perlu
```

### Reminder Tidak Terkirim
```
Solusi:
1. Pastikan siswa memiliki akun user
2. Cek koneksi database
3. Cek log error di sistem
4. Hubungi Admin IT
```

### Data Tidak Sesuai
```
Solusi:
1. Refresh halaman
2. Cek filter yang aktif
3. Cek tanggal di filter
4. Hubungi Admin IT jika masih salah
```

---

## 📱 Akses Mobile

Dashboard bisa diakses via HP:
```
1. Buka browser di HP
2. Ketik: sekolah.com/admin
3. Login
4. Semua widget responsive
5. Tabel bisa di-scroll horizontal
```

---

## 📈 Laporan Harian

### Data yang Perlu Dicatat:
```
📊 Total siswa hadir: ___
✅ Verifikasi lengkap: ___
⚠️ Belum lengkap: ___
📈 Persentase kelengkapan: ___%
🔔 Reminder terkirim: ___
📝 Catatan khusus: ___________
```

### Template Laporan:
```
LAPORAN VERIFIKASI ABSENSI
Tanggal: 09/12/2025

Total Hadir: 150 siswa
Verifikasi Lengkap: 142 siswa (94.7%)
Belum Lengkap: 8 siswa (5.3%)

Detail Belum Lengkap:
- Hanya QR: 5 siswa
- Hanya Manual: 3 siswa

Tindak Lanjut:
- Reminder terkirim: 8 siswa
- Follow up wali kelas: 2 siswa

Catatan:
- Kelas 12 IPA: 100% lengkap ✅
- Kelas 11 IPS: Perlu perhatian (85%)
```

---

## 🎓 Checklist Harian

### Pagi
- [ ] Login ke dashboard
- [ ] Cek widget verifikasi
- [ ] Monitor siswa check-in

### Siang
- [ ] Cek tabel belum lengkap
- [ ] Kirim reminder
- [ ] Catat jumlah belum lengkap

### Sore
- [ ] Cek tingkat kelengkapan akhir
- [ ] Follow up siswa bermasalah
- [ ] Buat laporan harian
- [ ] Share ke wali kelas

---

## 📞 Kontak Support

**Masalah Teknis**:
- Admin IT: it@sekolah.com
- Telp: 021-xxx-xxxx

**Pertanyaan Sistem**:
- Developer: dev@sekolah.com

---

## 🎯 Ringkasan Super Singkat

```
1. Cek Dashboard Widget 📊
   ↓
2. Lihat Tabel Belum Lengkap 📋
   ↓
3. Filter & Kirim Reminder 🔔
   ↓
4. Follow Up & Laporan 📈
   ↓
5. SELESAI! ✅
```

**INGAT**:
- 📊 Cek dashboard 3x sehari
- 🔔 Kirim reminder siang hari
- 📈 Target kelengkapan > 95%
- 📝 Buat laporan harian

---

**Selamat memonitor! 👨‍🏫**

---

**Dibuat**: 9 Desember 2025
**Versi**: 1.0.0
**Untuk**: Admin & Guru (Tutorial Monitoring)
