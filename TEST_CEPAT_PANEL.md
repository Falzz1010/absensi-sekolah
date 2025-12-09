# Test Cepat Panel - 2 Menit

## 🚀 Test 1: Login Admin (30 detik)

1. Buka browser **Incognito/Private** (penting!)
2. Akses: `http://localhost/admin`
3. Login:
   - Email: `admin@example.com`
   - Password: `password`
4. ✅ Harus masuk ke dashboard admin (warna kuning)
5. Lihat sidebar - ada menu:
   - Dashboard
   - Dashboard Overview
   - Data Murid ← Ini untuk KELOLA data siswa
   - Data Guru
   - dll

---

## 🚀 Test 2: Login Murid (30 detik)

1. **Logout** dari admin
2. Akses: `http://localhost/student`
3. Login:
   - Email: `murid@example.com`
   - Password: `password`
4. ✅ Harus masuk ke dashboard murid (warna biru)
5. Lihat sidebar - ada menu:
   - Dashboard ← Ini dashboard PRIBADI murid
   - Scan QR
   - Ajukan Izin
   - Riwayat Absensi
   - Profil Saya

---

## 🚀 Test 3: Security Check (30 detik)

### Test 3A: Murid tidak bisa akses admin
1. Masih login sebagai murid
2. Coba akses: `http://localhost/admin`
3. ✅ Harus error 403 atau redirect

### Test 3B: Admin tidak bisa akses student
1. Logout, login sebagai admin
2. Coba akses: `http://localhost/student`
3. ✅ Harus error 403 atau redirect

---

## 🚀 Test 4: Script Otomatis (30 detik)

```bash
php test-panel-access.php
```

Output yang benar:
```
✅ Admin: Can access admin panel, CANNOT access student panel
✅ Guru: Can access admin panel, CANNOT access student panel
✅ Murid: CANNOT access admin panel, Can access student panel
```

---

## ❓ Troubleshooting

### Masalah: Admin redirect ke student panel

**Solusi:**
1. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Hapus session:
   ```bash
   del storage\framework\sessions\*
   ```

3. Test di **Incognito mode** (penting!)

4. Pastikan login dengan email yang benar:
   - Admin: `admin@example.com`
   - Murid: `murid@example.com`

### Masalah: Lihat "Dashboard Murid" di admin panel

**Bukan bug!** Itu adalah menu "Data Murid" yang fungsinya untuk:
- Tambah murid baru
- Edit data murid
- Hapus murid
- Import Excel

Ini BERBEDA dengan "Dashboard" di panel murid yang untuk siswa lihat data pribadi.

---

## 📊 Perbedaan Cepat

| Item | Panel Admin | Panel Murid |
|------|-------------|-------------|
| URL | `/admin` | `/student` |
| Warna | 🟡 Kuning | 🔵 Biru |
| Menu "Data Murid" | ✅ Ada | ❌ Tidak |
| Menu "Dashboard" | ✅ Ada (overview) | ✅ Ada (pribadi) |
| Fungsi | Kelola semua | Lihat sendiri |

---

## ✅ Hasil yang Benar

Setelah test, harusnya:
- ✅ Admin bisa login di `/admin` (kuning)
- ✅ Murid bisa login di `/student` (biru)
- ✅ Admin TIDAK bisa akses `/student`
- ✅ Murid TIDAK bisa akses `/admin`
- ✅ Tidak ada duplikasi menu
- ✅ Security berfungsi

---

## 📞 Jika Masih Bermasalah

1. Baca: `PENJELASAN_DASHBOARD.md`
2. Baca: `SOLUSI_DASHBOARD_FINAL.md`
3. Jalankan: `php test-panel-access.php`
4. Screenshot masalahnya dan tunjukkan
