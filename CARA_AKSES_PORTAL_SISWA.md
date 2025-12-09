# 🎓 CARA AKSES PORTAL SISWA

## ✅ STATUS: SIAP DIGUNAKAN!

Semua fitur portal siswa sudah lengkap dan siap diakses!

---

## 📋 LANGKAH-LANGKAH AKSES

### 1️⃣ Pastikan Server Running

Server sudah running di background. Jika belum, jalankan:

```bash
php artisan serve
```

Output:
```
Server running on [http://localhost:8000]
```

### 2️⃣ Buka Browser

Buka browser favorit Anda (Chrome, Firefox, Edge, dll)

### 3️⃣ Akses URL Portal Siswa

Ketik di address bar:

```
http://localhost:8000/student
```

atau

```
http://127.0.0.1:8000/student
```

### 4️⃣ Login dengan Akun Siswa

Anda akan melihat halaman login. Masukkan:

```
Email: murid@example.com
Password: password
```

Klik tombol **"Sign in"**

### 5️⃣ Hard Refresh (Jika Perlu)

Jika setelah login dashboard tidak muncul, lakukan hard refresh:

**Windows/Linux**: `Ctrl + Shift + R`
**Mac**: `Cmd + Shift + R`

---

## 🎯 APA YANG AKAN ANDA LIHAT

Setelah login berhasil, Anda akan melihat **Dashboard Portal Siswa** dengan:

### 📱 Quick Action Buttons (Atas)
```
┌─────────────────────────────────────────┐
│  [📷 Scan QR Code]  [📝 Ajukan Izin]   │
└─────────────────────────────────────────┘
```

### 📊 4 Widget Informasi

#### 1. Kehadiran Hari Ini
- Status absensi hari ini (Hadir/Terlambat/Sakit/Izin/Alfa)
- Waktu check-in
- Tombol "Scan QR Code Sekarang" jika belum absen

#### 2. Ringkasan 30 Hari
- Total Hadir
- Total Sakit
- Total Izin
- Total Alfa
- Persentase kehadiran

#### 3. Jadwal Hari Ini
- Daftar mata pelajaran hari ini
- Nama guru
- Jam pelajaran
- Highlight kelas yang sedang berlangsung

#### 4. Notifikasi
- Notifikasi keterlambatan
- Update status izin/sakit
- Pengumuman penting

### 🧭 Menu Navigasi (Sidebar)

Klik menu di sidebar untuk akses fitur:

1. **🏠 Dashboard** - Halaman utama
2. **📷 Scan QR** - Scan QR code untuk absen
3. **📝 Ajukan Izin** - Upload bukti izin/sakit
4. **📊 Riwayat Absensi** - Lihat 30 hari terakhir
5. **👤 Profil** - Update foto dan info pribadi

---

## 🔹 FITUR A: MELAKUKAN ABSENSI

### 1. Scan QR di Kelas

**Cara:**
1. Klik menu **"Scan QR"** atau tombol **"Scan QR Code"**
2. Izinkan akses kamera saat diminta
3. Arahkan kamera ke QR code kelas
4. Sistem otomatis mencatat kehadiran

**Fitur:**
- ✅ Deteksi kamera otomatis
- ✅ Validasi QR code
- ✅ Cegah double check-in
- ✅ Responsive mobile

### 2. Upload Bukti Izin/Sakit

**Cara:**
1. Klik menu **"Ajukan Izin"** atau tombol **"Ajukan Izin/Sakit"**
2. Pilih tanggal
3. Pilih status: **Sakit** atau **Izin**
4. Tulis keterangan
5. Upload foto bukti:
   - Untuk Sakit: Foto surat dokter
   - Untuk Izin: Foto surat izin orang tua
6. Klik **"Kirim Pengajuan"**

**Fitur:**
- ✅ Camera capture di mobile
- ✅ Auto kompresi gambar
- ✅ Validasi file (max 2MB, jpg/png)
- ✅ Status verifikasi real-time

---

## 🔹 FITUR B: RIWAYAT ABSENSI PRIBADI

### 1. Lihat Absensi Hari Ini

**Lokasi:** Widget "Kehadiran Hari Ini" di Dashboard

**Info yang Ditampilkan:**
- Status kehadiran (dengan icon warna)
- Waktu check-in
- Durasi keterlambatan (jika terlambat)
- Status verifikasi (untuk izin/sakit)
- Keterangan
- Link dokumen bukti

### 2. Riwayat 30 Hari

**Cara:**
1. Klik menu **"Riwayat Absensi"**
2. Gunakan filter untuk mencari:
   - Filter berdasarkan status
   - Filter berdasarkan tanggal
3. Klik baris untuk lihat detail lengkap

**Fitur:**
- ✅ Tabel dengan pagination
- ✅ Filter multi-kriteria
- ✅ Detail modal
- ✅ Card view di mobile

### 3. Rekap Izin/Sakit/Alfa

**Lokasi:** Widget "Ringkasan 30 Hari" di Dashboard

**Info yang Ditampilkan:**
- Total Hadir (hijau)
- Total Sakit (biru)
- Total Izin (abu-abu)
- Total Alfa (merah)
- Persentase kehadiran

### 4. Notifikasi Terlambat

**Lokasi:** Widget "Notifikasi" di Dashboard

**Jenis Notifikasi:**
- ⚠️ Keterlambatan (dengan durasi)
- ✅ Izin/sakit disetujui
- ❌ Izin/sakit ditolak
- 📢 Pengumuman penting

---

## 🔹 FITUR C: PROFIL MURID

### 1. Update Foto

**Cara:**
1. Klik menu **"Profil"**
2. Scroll ke bagian "Upload Foto Profil"
3. Klik **"Pilih Foto"** atau gunakan kamera
4. Pilih foto dari galeri atau ambil foto baru
5. Klik **"Upload Foto"**

**Fitur:**
- ✅ Camera capture di mobile
- ✅ Auto kompresi (500x500px)
- ✅ Preview sebelum upload
- ✅ Validasi file

### 2. Lihat Info Lengkap

**Lokasi:** Menu "Profil"

**Info yang Ditampilkan:**
- Foto profil
- Nama lengkap
- NIS
- Email
- Kelas
- Wali Kelas

### 3. Lihat Jadwal Hari Ini

**Lokasi:** Widget "Jadwal Hari Ini" di Dashboard

**Info yang Ditampilkan:**
- Mata pelajaran
- Nama guru
- Jam mulai - selesai
- Highlight kelas yang sedang berlangsung

---

## 📱 MOBILE RESPONSIVE

Semua fitur sudah dioptimasi untuk mobile:

- ✅ Layout responsive (320px - 768px)
- ✅ Touch-friendly buttons (min 44px)
- ✅ Camera access untuk QR scan & upload
- ✅ Auto kompresi gambar
- ✅ Card view untuk tabel
- ✅ Hamburger menu

---

## 🔧 TROUBLESHOOTING

### Dashboard Tidak Muncul Setelah Login?

**Solusi:**

1. **Clear Cache**
```bash
php artisan optimize:clear
php artisan filament:optimize-clear
```

2. **Hard Refresh Browser**
- Windows/Linux: `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`

3. **Logout dan Login Lagi**
- Klik profil di pojok kanan atas
- Klik "Logout"
- Login kembali dengan `murid@example.com`

4. **Cek Browser Console**
- Tekan `F12`
- Lihat tab "Console"
- Screenshot error jika ada

### Kamera Tidak Bisa Diakses?

**Solusi:**

1. Pastikan browser memiliki izin akses kamera
2. Cek pengaturan browser → Privacy → Camera
3. Reload halaman dan izinkan akses kamera
4. Gunakan HTTPS jika di production

### Upload Foto Gagal?

**Solusi:**

1. Pastikan ukuran file < 2MB
2. Pastikan format jpg atau png
3. Coba kompres foto terlebih dahulu
4. Cek koneksi internet

---

## 📞 BANTUAN LEBIH LANJUT

Jika masih ada masalah, cek dokumentasi:

- `TROUBLESHOOTING_STUDENT_PORTAL.md` - Panduan troubleshooting lengkap
- `STUDENT_PORTAL_COMPLETE.md` - Status & fitur lengkap
- `FRONTEND_SUDAH_ADA.md` - Bukti semua frontend yang ada

Atau jalankan verifikasi:
```bash
php verify-student-portal.php
```

---

## ✅ CHECKLIST AKSES

- [ ] Server running di `http://localhost:8000`
- [ ] Cache sudah di-clear
- [ ] Browser sudah dibuka
- [ ] URL `http://localhost:8000/student` sudah diakses
- [ ] Login dengan `murid@example.com` / `password`
- [ ] Dashboard muncul dengan 4 widgets
- [ ] Menu navigasi terlihat di sidebar
- [ ] Quick action buttons terlihat di atas

Jika semua checklist ✅, portal siap digunakan! 🎉

---

**Status**: PRODUCTION READY 🚀
**Last Updated**: 7 Desember 2025
