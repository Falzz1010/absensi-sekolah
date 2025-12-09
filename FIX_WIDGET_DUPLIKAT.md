# Fix Widget Duplikat - Dashboard Murid

## 🐛 Masalah

Di dashboard murid (panel biru `/student`) muncul widget admin yang menampilkan:
- Total Murid (22)
- Total Guru (6)
- Kehadiran Hari Ini (0/0)
- Chart statistik 7 hari terakhir

Widget ini seharusnya **HANYA** muncul di panel admin (kuning), bukan di panel murid.

## 🔍 Root Cause

Widget admin (`StatsOverview`, `AbsensiChart`, dll) tidak memiliki authorization check, sehingga bisa muncul di panel manapun yang melakukan auto-discovery.

## ✅ Solusi

### 1. Tambah Authorization ke Semua Widget Admin

Menambahkan method `canView()` ke setiap widget admin:

**File yang diupdate:**
- `app/Filament/Widgets/StatsOverview.php`
- `app/Filament/Widgets/AbsensiChart.php`
- `app/Filament/Widgets/RekapMingguan.php`
- `app/Filament/Widgets/RekapBulanan.php`
- `app/Filament/Widgets/RankingKehadiranKelas.php`
- `app/Filament/Widgets/RekapAbsensiKelas.php`

**Kode yang ditambahkan:**
```php
// Only show in admin panel
public static function canView(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'guru']);
}
```

### 2. Explicit Widget Registration di Student Panel

Update `app/Providers/Filament/StudentPanelProvider.php`:

```php
->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
->widgets([
    // Explicitly register only student widgets
    \App\Filament\Student\Widgets\TodayAttendanceWidget::class,
    \App\Filament\Student\Widgets\NotificationsWidget::class,
    \App\Filament\Student\Widgets\AttendanceSummaryWidget::class,
    \App\Filament\Student\Widgets\TodayScheduleWidget::class,
])
```

## 📋 Widget yang Benar

### Panel Admin (Kuning) - `/admin`
✅ **StatsOverview** - Total Murid, Total Guru, Kehadiran Hari Ini
✅ **AbsensiChart** - Chart 7 hari terakhir
✅ **RekapMingguan** - Rekap minggu ini
✅ **RekapBulanan** - Rekap bulan ini
✅ **RankingKehadiranKelas** - Ranking kelas
✅ **RekapAbsensiKelas** - Rekap per kelas

### Panel Murid (Biru) - `/student`
✅ **TodayAttendanceWidget** - Absensi hari ini (pribadi)
✅ **NotificationsWidget** - Notifikasi pribadi
✅ **AttendanceSummaryWidget** - Ringkasan 30 hari (pribadi)
✅ **TodayScheduleWidget** - Jadwal hari ini

## 🧪 Cara Test

### Test 1: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Test 2: Login sebagai Murid
1. Buka browser **Incognito**
2. Akses: `http://localhost/student`
3. Login: `murid@example.com` / `password`
4. Lihat dashboard

**Yang HARUS muncul:**
- ✅ Absensi Hari Ini (data pribadi)
- ✅ Notifikasi
- ✅ Ringkasan Kehadiran (Hadir, Terlambat, Sakit, Izin, Alfa - data pribadi)
- ✅ Jadwal Hari Ini

**Yang TIDAK BOLEH muncul:**
- ❌ Total Murid
- ❌ Total Guru
- ❌ Chart statistik sekolah
- ❌ Rekap mingguan/bulanan sekolah

### Test 3: Login sebagai Admin
1. Logout dari murid
2. Akses: `http://localhost/admin`
3. Login: `admin@example.com` / `password`
4. Lihat dashboard

**Yang HARUS muncul:**
- ✅ Total Murid
- ✅ Total Guru
- ✅ Kehadiran Hari Ini (seluruh sekolah)
- ✅ Chart 7 hari terakhir
- ✅ Rekap mingguan
- ✅ Rekap bulanan

## 📊 Perbandingan

| Widget | Panel Admin | Panel Murid |
|--------|-------------|-------------|
| Total Murid/Guru | ✅ | ❌ |
| Chart Statistik Sekolah | ✅ | ❌ |
| Rekap Mingguan/Bulanan | ✅ | ❌ |
| Absensi Hari Ini (Pribadi) | ❌ | ✅ |
| Jadwal Hari Ini (Pribadi) | ❌ | ✅ |
| Notifikasi Pribadi | ❌ | ✅ |
| Ringkasan 30 Hari (Pribadi) | ❌ | ✅ |

## 🔐 Security Check

Setiap widget admin sekarang memiliki check:
```php
public static function canView(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'guru']);
}
```

Ini memastikan:
- ✅ Murid TIDAK bisa lihat widget admin
- ✅ Admin/Guru bisa lihat widget admin
- ✅ Widget student hanya untuk murid

## ✅ Status

- [x] Tambah authorization ke semua widget admin
- [x] Explicit registration widget di student panel
- [x] Clear cache
- [x] Dokumentasi lengkap
- [ ] **Test di browser** (perlu dilakukan user)

## 📝 Catatan

Jika setelah clear cache masih muncul widget admin di panel murid:
1. Restart web server (Apache/Nginx)
2. Test di Incognito mode
3. Periksa apakah ada widget lain yang ter-discover
4. Jalankan: `php artisan optimize:clear`
