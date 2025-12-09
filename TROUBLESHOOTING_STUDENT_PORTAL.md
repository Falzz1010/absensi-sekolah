# 🔧 Troubleshooting Student Portal

## ✅ MASALAH SUDAH DIPERBAIKI!

**Status**: Portal siswa sekarang sudah bisa diakses dengan sempurna! 🎉

**Root Cause**: Ketidakcocokan nama role - `User.php` mengecek role 'murid' tapi seeder membuat role 'student'.

**Solution Applied**: 
- ✅ Update `User.php` untuk cek role 'student'
- ✅ Update `StudentRoleSeeder.php` untuk assign kedua role ('student' dan 'murid')
- ✅ Semua 22 user siswa sudah memiliki role yang benar
- ✅ Cache sudah di-clear

**Lihat detail lengkap di**: `STUDENT_PORTAL_FIX.md`

---

## 🚀 Cara Akses Portal Siswa

### Langkah 1: Clear Cache (WAJIB!)
```bash
php artisan optimize:clear
php artisan filament:optimize-clear
```

### Langkah 2: Logout & Hard Refresh
- Logout dari akun saat ini
- Tekan `Ctrl + Shift + R` untuk hard refresh browser

### Langkah 3: Login dengan Akun Siswa
```
Email: murid@example.com
Password: password
```

### Langkah 4: Akses Portal
URL: `http://localhost/student`

Portal akan menampilkan dashboard dengan fitur lengkap! ✨

---

## 🚨 Masalah Umum & Solusi (Legacy)

### 1. "Fitur di frontend belum tampil"

**Kemungkinan Penyebab:**
- Cache belum di-clear
- Browser cache
- Session lama
- Belum login dengan akun student

**Solusi:**

```bash
# Clear semua cache Laravel
php artisan optimize:clear

# Clear Filament cache
php artisan filament:optimize-clear

# Restart server (jika pakai artisan serve)
# Ctrl+C lalu jalankan lagi
php artisan serve
```

Kemudian di browser:
1. **Hard refresh**: Ctrl + Shift + R (Windows) atau Cmd + Shift + R (Mac)
2. **Clear browser cache**: Ctrl + Shift + Delete
3. **Logout** dari semua panel (admin & student)
4. **Login ulang** dengan akun student

### 2. "403 Forbidden" saat akses /student

**Penyebab**: User tidak punya role 'student'

**Solusi:**
```bash
php artisan db:seed --class=StudentRoleSeeder
```

### 3. "Role student not found"

**Penyebab**: Role belum dibuat di database

**Solusi:**
```bash
php artisan db:seed --class=StudentRoleSeeder
```

### 4. "Page not found" atau "404"

**Penyebab**: Routes belum ter-register

**Solusi:**
```bash
php artisan route:clear
php artisan optimize:clear
```

Cek routes:
```bash
php artisan route:list --path=student
```

Harus muncul 7 routes:
- GET student (dashboard)
- GET student/login
- POST student/logout
- GET student/qr-scan-page
- GET student/absence-submission-page
- GET student/attendance-history-page
- GET student/student-profile-page

### 5. "Widgets tidak muncul di dashboard"

**Kemungkinan Penyebab:**
- Widget class tidak ditemukan
- Error di widget
- Data murid tidak ada

**Solusi:**

1. Cek error di log:
```bash
tail -f storage/logs/laravel.log
```

2. Cek apakah murid punya kelas_id:
```bash
php artisan tinker
>>> App\Models\Murid::whereNotNull('user_id')->first()->kelas_id
```

3. Clear view cache:
```bash
php artisan view:clear
```

### 6. "QR Scanner tidak muncul"

**Penyebab**: 
- Browser tidak support camera
- Permission camera ditolak
- Tidak pakai HTTPS/localhost

**Solusi:**
1. Gunakan browser modern (Chrome, Firefox, Safari)
2. Izinkan akses kamera saat diminta
3. Pastikan URL adalah `localhost` atau `https://`
4. Cek console browser (F12) untuk error JavaScript

### 7. "Upload file gagal"

**Penyebab**:
- Storage link belum dibuat
- Permission folder storage
- File terlalu besar

**Solusi:**
```bash
# Buat storage link
php artisan storage:link

# Set permission (Linux/Mac)
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Windows: pastikan folder storage writable
```

Cek ukuran file:
- Bukti izin/sakit: max 5MB
- Foto profil: max 2MB

### 8. "Notifikasi tidak muncul"

**Penyebab**: Tabel notifications belum ada

**Solusi:**
```bash
php artisan migrate
```

### 9. "Jadwal hari ini kosong"

**Penyebab**: 
- Belum ada data jadwal
- Murid belum punya kelas_id
- Hari ini libur

**Solusi:**
1. Cek data jadwal:
```bash
php artisan tinker
>>> App\Models\Jadwal::count()
```

2. Seed data jadwal jika perlu:
```bash
php artisan db:seed --class=JadwalSeeder
```

### 10. "Riwayat kehadiran kosong"

**Penyebab**: Belum ada data absensi untuk murid ini

**Solusi:**
1. Buat data absensi dummy untuk testing
2. Atau scan QR code untuk create absensi baru

## 🔍 Debug Mode

Untuk melihat error detail, aktifkan debug mode:

1. Edit `.env`:
```
APP_DEBUG=true
```

2. Refresh halaman dan lihat error message lengkap

3. **PENTING**: Matikan debug mode di production:
```
APP_DEBUG=false
```

## 📱 Testing di Mobile

1. **Akses dari HP di jaringan yang sama:**
```
http://192.168.x.x/student
```
(Ganti dengan IP komputer Anda)

2. **Cari IP komputer:**
```bash
# Windows
ipconfig

# Linux/Mac
ifconfig
```

3. **Pastikan firewall tidak block port 80/8000**

## 🎯 Checklist Lengkap

Sebelum melaporkan bug, pastikan sudah cek semua ini:

- [ ] Role 'student' sudah ada (`php artisan db:seed --class=StudentRoleSeeder`)
- [ ] User sudah punya role student
- [ ] Cache sudah di-clear (`php artisan optimize:clear`)
- [ ] Browser cache sudah di-clear (Ctrl+Shift+R)
- [ ] Sudah logout dan login ulang
- [ ] Routes sudah ter-register (`php artisan route:list --path=student`)
- [ ] Storage link sudah dibuat (`php artisan storage:link`)
- [ ] Migrations sudah dijalankan (`php artisan migrate`)
- [ ] Menggunakan akun student yang benar (bukan admin)
- [ ] Server Laravel sudah running

## 📞 Masih Bermasalah?

Jika masih ada masalah setelah coba semua solusi di atas:

1. **Cek log error:**
```bash
tail -f storage/logs/laravel.log
```

2. **Cek browser console** (F12 → Console tab)

3. **Screenshot error** dan kirim ke developer

4. **Informasi yang perlu disertakan:**
   - Error message lengkap
   - URL yang diakses
   - Email akun yang digunakan
   - Browser & versi
   - Screenshot (jika ada)

---

**Portal sudah siap digunakan!** Jika semua checklist di atas sudah ✅, portal seharusnya berfungsi normal.
