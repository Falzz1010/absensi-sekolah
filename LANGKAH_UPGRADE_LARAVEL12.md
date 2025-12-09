# Langkah-Langkah Upgrade ke Laravel 12

## ✅ Persiapan

### 1. Backup Dulu!
```bash
# Backup database (jika ada command)
php artisan db:backup

# Atau manual backup database dari phpMyAdmin/MySQL
# Export database: absensi_sekolah

# Backup folder project
# Copy folder ke: Absensi-Sekolah-backup
```

### 2. Commit Git (Opsional)
```bash
git add .
git commit -m "Before Laravel 12 upgrade"
```

## 🚀 Proses Upgrade

### Cara 1: Menggunakan Script Otomatis

```bash
# Jalankan script upgrade
upgrade-to-laravel12.bat
```

Script akan otomatis:
1. Backup database
2. Clear cache
3. Update composer
4. Publish config
5. Run migrations
6. Rebuild assets
7. Run tests

### Cara 2: Manual Step by Step

#### Step 1: Update composer.json
File `composer.json` sudah diupdate ke:
```json
"laravel/framework": "^12.0"
```

#### Step 2: Update Dependencies
```bash
composer update
```

Tunggu proses selesai (5-10 menit tergantung koneksi).

#### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

#### Step 4: Publish Config (Opsional)
```bash
php artisan vendor:publish --tag=laravel-config
```

Pilih `[0] All` jika diminta.

#### Step 5: Run Migrations
```bash
php artisan migrate
```

Jika ada error, cek dan fix manual.

#### Step 6: Rebuild Assets
```bash
npm install
npm run build
```

#### Step 7: Optimize
```bash
php artisan optimize
```

## 🧪 Testing

### 1. Start Server
```bash
php artisan serve
```

### 2. Test Manual

**Admin Panel:**
- Login admin: http://localhost:8000/admin
- Cek Dashboard Overview
- Test CRUD (Kelas, Murid, Guru)
- Test Pengaturan
- Test Laporan

**Student Panel:**
- Login murid: http://localhost:8000/student
- Cek Dashboard
- Test QR Scanner
- Test Ajukan Izin
- Test Riwayat Absensi

**Realtime:**
- Test notifikasi realtime
- Test auto-refresh dashboard

### 3. Run Automated Tests
```bash
php artisan test
```

## ⚠️ Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Error: "Config cache"
```bash
php artisan config:clear
php artisan config:cache
```

### Error: "Route not found"
```bash
php artisan route:clear
php artisan route:cache
```

### Error: "View not found"
```bash
php artisan view:clear
```

### Error: Database migration
```bash
# Rollback last migration
php artisan migrate:rollback

# Run again
php artisan migrate
```

### Error: Composer dependencies
```bash
# Remove vendor
rmdir /s /q vendor

# Reinstall
composer install
```

## 📋 Checklist Post-Upgrade

- [ ] Server berjalan tanpa error
- [ ] Login admin berhasil
- [ ] Login guru berhasil
- [ ] Login murid berhasil
- [ ] Dashboard tampil dengan benar
- [ ] CRUD berfungsi normal
- [ ] QR Scanner berfungsi
- [ ] Realtime notification berfungsi
- [ ] Export laporan berfungsi
- [ ] Import Excel berfungsi
- [ ] Auto logout berfungsi
- [ ] Alert system berfungsi

## 🔄 Rollback (Jika Gagal)

### 1. Restore Database
```sql
-- Import backup database
-- Dari phpMyAdmin atau MySQL command
```

### 2. Restore Files
```bash
# Copy dari backup folder
xcopy /E /I ..\Absensi-Sekolah-backup\* .
```

### 3. Downgrade Laravel
Edit `composer.json`:
```json
"laravel/framework": "^11.31"
```

```bash
composer update
php artisan optimize:clear
```

## 📊 Perbandingan Versi

| Fitur | Laravel 11 | Laravel 12 |
|-------|-----------|-----------|
| PHP Version | 8.2+ | 8.2+ |
| Performance | Fast | Faster |
| Security | Good | Better |
| Features | Stable | Enhanced |

## 🎯 Breaking Changes di Laravel 12

### 1. Tidak Ada Breaking Changes Major
Laravel 12 backward compatible dengan Laravel 11 untuk sebagian besar fitur.

### 2. Deprecations
Beberapa method deprecated, tapi masih berfungsi dengan warning.

### 3. New Features
- Improved performance
- Better error handling
- Enhanced security
- New helper functions

## ✅ Status Upgrade

**Current Status**: Ready to upgrade

**Files Modified**:
- ✅ composer.json (Laravel 12.0)
- ✅ upgrade-to-laravel12.bat (Script otomatis)
- ✅ UPGRADE_LARAVEL_12.md (Dokumentasi)

**Next Steps**:
1. Run `composer update`
2. Test aplikasi
3. Deploy ke production (jika test berhasil)

## 📞 Support

Jika ada masalah saat upgrade:
1. Check error log: `storage/logs/laravel.log`
2. Check browser console untuk error JavaScript
3. Check database connection
4. Hubungi developer team

## 🎉 Setelah Upgrade Berhasil

1. Update dokumentasi
2. Inform users tentang upgrade
3. Monitor aplikasi 24-48 jam
4. Backup database secara berkala

---

**Last Updated**: December 2024
**Laravel Version**: 12.0
**Status**: Ready to Upgrade ✅
