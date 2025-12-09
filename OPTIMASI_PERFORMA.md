# ⚡ Optimasi Performa - Dashboard

## 🐌 Masalah Loading Lambat

Dashboard loading lambat (3-19 detik) karena:
1. **6 Widget** yang load bersamaan
2. **Banyak query database** tanpa caching
3. **Polling interval** terlalu cepat (default)
4. **Tidak ada lazy loading**

## ✅ Solusi yang Sudah Diterapkan

### 1. Polling Interval
Menambahkan polling interval untuk mengurangi auto-refresh:

```php
// StatsOverview - refresh setiap 30 detik
protected static ?string $pollingInterval = '30s';

// AbsensiChart - refresh setiap 60 detik
protected static ?string $pollingInterval = '60s';

// RekapMingguan - refresh setiap 2 menit
protected static ?string $pollingInterval = '120s';

// RekapBulanan - refresh setiap 2 menit
protected static ?string $pollingInterval = '120s';

// RankingKehadiranKelas - refresh setiap 2 menit
protected static ?string $pollingInterval = '120s';

// RekapAbsensiKelas - refresh setiap 60 detik
protected static ?string $pollingInterval = '60s';
```

### 2. Sort Order
Widget diurutkan berdasarkan prioritas:
1. StatsOverview (paling penting)
2. AbsensiChart
3. RekapMingguan
4. RekapBulanan
5. RankingKehadiranKelas
6. RekapAbsensiKelas

## 🚀 Optimasi Tambahan (Optional)

### A. Lazy Load Widget
Tambahkan lazy loading untuk widget yang tidak urgent:

```php
// Di widget yang tidak urgent
protected static bool $isLazy = true;
```

### B. Cache Query
Tambahkan caching untuk query yang sering diakses:

```php
use Illuminate\Support\Facades\Cache;

protected function getStats(): array
{
    return Cache::remember('stats_overview', 60, function () {
        // Query database
        return [/* stats */];
    });
}
```

### C. Disable Widget Sementara
Jika masih lambat, disable beberapa widget:

```php
// Di AdminPanelProvider.php
->widgets([
    StatsOverview::class,
    AbsensiChart::class,
    // RekapMingguan::class, // Disable
    // RekapBulanan::class, // Disable
    // RankingKehadiranKelas::class, // Disable
    // RekapAbsensiKelas::class, // Disable
])
```

### D. Database Index
Tambahkan index untuk kolom yang sering di-query:

```php
// Migration
$table->index('tanggal');
$table->index('status');
$table->index('kelas');
$table->index(['tanggal', 'status']);
```

### E. Optimize Query
Gunakan raw query untuk agregasi:

```php
// Sebelum (N+1 query)
$data = Absensi::all()->groupBy('status');

// Sesudah (1 query)
$data = Absensi::select('status', DB::raw('COUNT(*) as total'))
    ->groupBy('status')
    ->get();
```

## 📊 Perbandingan Performa

### Sebelum Optimasi:
- Load time: 15-19 detik
- Auto-refresh: Setiap 5 detik (default)
- Memory: High

### Sesudah Optimasi:
- Load time: 3-5 detik (expected)
- Auto-refresh: 30s - 120s
- Memory: Medium

## 🎯 Rekomendasi

### Untuk Development:
- Gunakan semua widget dengan polling interval
- Monitor dengan Laravel Debugbar

### Untuk Production:
- Tambahkan caching
- Tambahkan database index
- Gunakan lazy loading
- Consider Redis untuk caching

### Untuk Sekolah Kecil (< 200 siswa):
- Semua widget OK
- Polling 30-60 detik

### Untuk Sekolah Besar (> 500 siswa):
- Disable beberapa widget
- Tambahkan caching
- Gunakan queue untuk heavy query
- Consider pagination

## 🔧 Testing

### Test Load Time:
```bash
# Clear cache
php artisan optimize:clear

# Reload dashboard
# Check browser DevTools > Network
# Lihat waktu load untuk /admin
```

### Test Query Performance:
```bash
# Install Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev

# Reload dashboard
# Lihat jumlah query di bottom bar
```

## ✅ Status

- ✅ Polling interval ditambahkan
- ✅ Sort order dioptimasi
- ⏳ Caching (optional)
- ⏳ Database index (optional)
- ⏳ Lazy loading (optional)

Refresh browser untuk melihat perubahan!
