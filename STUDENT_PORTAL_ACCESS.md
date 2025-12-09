# 🎓 Panduan Akses Student Portal

## ✅ Status: Portal Sudah Aktif!

Student Portal sudah berhasil diimplementasi dan siap digunakan.

## 🔗 URL Akses

```
http://localhost/student
atau
http://localhost/student/login
```

## 👤 Akun Student untuk Testing

Gunakan salah satu akun berikut untuk login:

### Akun 1
- **Email**: `murid@example.com`
- **Password**: `password`
- **Nama**: Ahmad Fauzi

### Akun 2
- **Email**: `siti.nur@student.com`
- **Password**: `password`
- **Nama**: Siti Nurhaliza

### Akun 3
- **Email**: `budi.santoso@student.com`
- **Password**: `password`
- **Nama**: Budi Santoso

### Akun 4
- **Email**: `dewi.lestari@student.com`
- **Password**: `password`
- **Nama**: Dewi Lestari

### Akun 5
- **Email**: `rizki.pratama@student.com`
- **Password**: `password`
- **Nama**: Rizki Pratama

**Total**: 22 akun student tersedia

## 📱 Fitur yang Tersedia

### 🔹 A. Melakukan Absensi
- ✅ **Scan QR Code** - Halaman `/student/qr-scan-page`
- ✅ **Self Check-in** - Otomatis saat scan QR berhasil
- ✅ **Upload Bukti Izin/Sakit** - Halaman `/student/absence-submission-page`
  - Foto surat dokter (JPEG, PNG, PDF max 5MB)
  - Foto izin orang tua
  - Kompresi otomatis untuk mobile

### 🔹 B. Riwayat Absensi Pribadi
- ✅ **Dashboard** - Lihat absensi hari ini di `/student`
- ✅ **Riwayat 30 Hari** - Halaman `/student/attendance-history-page`
- ✅ **Rekap Statistik** - Widget di dashboard
  - Hadir, Terlambat, Sakit, Izin, Alfa
  - Highlight jika melebihi batas
- ✅ **Notifikasi** - Bell icon di header
  - Notifikasi terlambat
  - Status verifikasi izin/sakit

### 🔹 C. Profil Murid
- ✅ **Profil Page** - Halaman `/student/student-profile-page`
- ✅ **Update Foto** - Upload foto profil (JPEG, PNG max 2MB)
- ✅ **Info Kelas** - Nama kelas dan wali kelas
- ✅ **Jadwal Hari Ini** - Mata pelajaran dengan highlight kelas berlangsung

## 📱 Mobile Responsive

Portal sudah dioptimasi untuk mobile:
- ✅ Responsive layout (320px - 768px)
- ✅ Touch-friendly buttons (min 44px)
- ✅ QR scanner dengan akses kamera mobile
- ✅ File upload dari kamera/galeri
- ✅ Image compression otomatis

## 🔧 Troubleshooting

### Portal tidak bisa diakses?
1. Pastikan server Laravel sudah running: `php artisan serve`
2. Cek URL: `http://localhost:8000/student` atau `http://localhost/student`
3. Clear cache: `php artisan cache:clear`

### Tidak bisa login?
1. Pastikan menggunakan email dan password yang benar
2. Default password semua akun: `password`
3. Cek role student sudah ter-assign: `php artisan db:seed --class=StudentRoleSeeder`

### QR Scanner tidak muncul?
1. Izinkan akses kamera di browser
2. Gunakan HTTPS atau localhost
3. Test di browser modern (Chrome, Firefox, Safari)

### Upload file gagal?
1. Cek ukuran file (max 5MB untuk bukti, 2MB untuk foto profil)
2. Format file: JPEG, PNG, atau PDF
3. Pastikan folder storage writable: `php artisan storage:link`

## 🚀 Cara Generate Akun Student Baru

Jika ada murid baru yang perlu akun:

```bash
php artisan app:generate-student-accounts
```

Command ini akan:
- Membuat User untuk setiap Murid yang belum punya akun
- Email: `{nama_depan}.{nama_belakang}@student.com`
- Password default: `password`
- Assign role 'student' otomatis

## 📊 Statistik

- **Total Murid**: 22
- **Murid dengan Akun**: 22
- **Role Student**: ✅ Aktif
- **Panel Path**: `/student`
- **Halaman**: 5 (Dashboard, QR Scan, Absence, History, Profile)
- **Widgets**: 4 (Today Attendance, Summary, Schedule, Notifications)

## 🎯 Next Steps

1. **Login** ke portal dengan salah satu akun di atas
2. **Test QR Scanner** - Scan QR code yang ada di sistem
3. **Upload Bukti** - Coba ajukan izin/sakit dengan upload dokumen
4. **Cek Riwayat** - Lihat history absensi 30 hari
5. **Update Profil** - Upload foto profil

---

**Portal Student sudah siap digunakan!** 🎉

Untuk pertanyaan atau issue, silakan hubungi administrator sistem.
