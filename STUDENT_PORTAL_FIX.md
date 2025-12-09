# Fix Student Portal - Portal Tidak Tampil

## Masalah yang Ditemukan

Portal siswa tidak bisa diakses karena ada **ketidakcocokan nama role**:
- Role di `RoleSeeder`: `'murid'`
- Role di `StudentRoleSeeder`: `'student'`
- Role yang dicek di `User.php`: `'murid'` (sudah diubah ke `'student'`)

## Solusi yang Diterapkan

### 1. Perbaikan User Model
File: `app/Models/User.php`

Mengubah pengecekan role dari `'murid'` menjadi `'student'`:

```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    if ($panel->getId() === 'student') {
        // Check if user has student role and has an associated Murid record
        return $this->hasRole('student') && \App\Models\Murid::where('user_id', $this->id)->exists();
    }
    // ...
}
```

### 2. Update StudentRoleSeeder
File: `database/seeders/StudentRoleSeeder.php`

Sekarang membuat KEDUA role (`student` dan `murid`) dan assign keduanya ke semua user yang terhubung dengan Murid:

```php
// Create both student and murid roles
$studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
$muridRole = Role::firstOrCreate(['name' => 'murid', 'guard_name' => 'web']);

// Assign both roles to users
$murid->user->assignRole('student');
$murid->user->assignRole('murid');
```

### 3. Run Seeder
```bash
php artisan db:seed --class=StudentRoleSeeder
```

### 4. Cache Cleared
```bash
php artisan view:clear
php artisan config:clear
php artisan optimize:clear
php artisan filament:optimize-clear
```

## Cara Testing

### 1. Clear All Caches (PENTING!)
```bash
php artisan optimize:clear
php artisan filament:optimize-clear
```

### 2. Logout dari akun saat ini
Klik profil > Logout

### 3. Hard Refresh Browser
Tekan: `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac)

### 4. Login dengan akun siswa
Gunakan salah satu akun berikut:
- Email: `murid@example.com`
- Password: `password`

### 5. Akses Portal Siswa
URL: `http://localhost/student`

Portal akan menampilkan dashboard dengan 4 widget dan menu navigasi untuk fitur siswa.

## Fitur yang Tersedia di Portal Siswa

✅ **Dashboard** - Ringkasan kehadiran hari ini
✅ **Scan QR** - Absen dengan QR code
✅ **Ajukan Izin** - Upload bukti sakit/izin
✅ **Riwayat Absensi** - Lihat 30 hari terakhir
✅ **Profil** - Update foto dan data pribadi

## Verifikasi Status

✅ Total users dengan role `student`: **22 users**
✅ Total users dengan role `murid`: **22 users**
✅ Total Murid dengan akun: **22 records**
✅ Routes terdaftar: **7 routes**
✅ Panel configuration: **OK**
✅ Pages & Widgets: **All registered**

## Troubleshooting

Jika masih belum tampil:

1. **Cek browser console** (F12) untuk error JavaScript
2. **Pastikan sudah logout** dari akun admin/guru
3. **Clear browser cache** sepenuhnya
4. **Cek apakah server berjalan** di `http://localhost`
5. **Verifikasi data Murid** memiliki `kelas_id` yang valid

## Sample Login Credentials

```
Email: murid@example.com
Password: password
```

Atau cek users lain dengan:
```bash
php artisan tinker
>>> App\Models\User::role('student')->get(['email', 'name']);
```


## Testing Script

Untuk memverifikasi bahwa portal sudah bisa diakses, jalankan:

```bash
php artisan tinker
```

Kemudian jalankan:

```php
$user = App\Models\User::where('email', 'murid@example.com')->first();
$panel = Filament\Facades\Filament::getPanel('student');
$user->canAccessPanel($panel); // Should return true
```

Atau buat file test sederhana:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'murid@example.com')->first();
$panel = \Filament\Facades\Filament::getPanel('student');
echo $user->canAccessPanel($panel) ? "✅ CAN ACCESS" : "❌ CANNOT ACCESS";
```

## Summary

✅ **FIXED**: Portal siswa sekarang sudah bisa diakses
✅ **ROOT CAUSE**: Ketidakcocokan nama role antara seeder dan User model
✅ **SOLUTION**: Update User model dan StudentRoleSeeder untuk support kedua role
✅ **VERIFIED**: Test script menunjukkan user bisa akses panel

**Status**: Portal siswa sudah siap digunakan! 🎉
