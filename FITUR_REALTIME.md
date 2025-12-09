# 🔴 Fitur Real-Time dengan Laravel Reverb

## ✅ Fitur yang Sudah Real-Time

### 1. **Dashboard Widgets** (Auto Refresh)
- ✅ StatsOverview - refresh setiap 30 detik
- ✅ AbsensiChart - refresh setiap 60 detik  
- ✅ RekapMingguan - refresh setiap 120 detik
- ✅ RekapBulanan - refresh setiap 120 detik
- ✅ RankingKehadiranKelas - refresh setiap 120 detik
- ✅ RekapAbsensiKelas - refresh setiap 60 detik

### 2. **Resource Tables** (Auto Refresh)
- ✅ Absensi - refresh setiap 30 detik
- ✅ Murid - refresh setiap 60 detik
- ✅ Guru - refresh setiap 60 detik

### 3. **Broadcasting Events** (WebSocket)
- ✅ QrCodeScanned - notifikasi real-time saat QR code di-scan
- ✅ AbsensiCreated - update otomatis saat absensi baru dibuat
- ✅ AbsensiUpdated - update otomatis saat absensi diubah

### 4. **Notifications**
- ✅ Database notifications polling - 30 detik
- ✅ Real-time notifications via WebSocket

### 5. **SPA Mode**
- ✅ Navigasi tanpa reload halaman
- ✅ Live updates untuk semua komponen

## 🚀 Cara Menjalankan

### 1. Start Laravel Reverb Server
```bash
php artisan reverb:start
```

### 2. Start Queue Worker (untuk broadcasting)
```bash
php artisan queue:work
```

### 3. Start Development Server
```bash
php artisan serve
```

### 4. Build Assets (development)
```bash
npm run dev
```

Atau untuk production:
```bash
npm run build
```

## 📡 Konfigurasi Broadcasting

### Environment Variables (.env)
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=977187
REVERB_APP_KEY=rfnc8uqyeybnqpvtum4w
REVERB_APP_SECRET=enxxrgupnu7t53ryxeax
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 🎯 Event Broadcasting

### Events yang Tersedia:

#### 1. QrCodeScanned
Triggered saat murid scan QR code untuk absensi.

**Data:**
- `murid_name` - Nama murid
- `status` - Status absensi (Hadir/Sakit/Izin/Alfa)
- `kelas` - Kelas murid
- `tanggal` - Tanggal absensi
- `waktu` - Waktu scan

#### 2. AbsensiCreated
Triggered saat absensi baru dibuat.

**Data:**
- `id` - ID absensi
- `murid_id` - ID murid
- `status` - Status absensi
- `kelas` - Kelas
- `tanggal` - Tanggal

#### 3. AbsensiUpdated
Triggered saat absensi diupdate.

**Data:**
- `id` - ID absensi
- `murid_id` - ID murid
- `status` - Status absensi (updated)
- `kelas` - Kelas
- `tanggal` - Tanggal

## 🔧 Implementasi Teknis

### Observer Pattern
```php
// app/Observers/AbsensiObserver.php
class AbsensiObserver
{
    public function created(Absensi $absensi): void
    {
        broadcast(new AbsensiCreated($absensi))->toOthers();
    }

    public function updated(Absensi $absensi): void
    {
        broadcast(new AbsensiUpdated($absensi))->toOthers();
    }
}
```

### Broadcasting Events
```php
// app/Events/QrCodeScanned.php
class QrCodeScanned implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [new Channel('absensi')];
    }
}
```

### Frontend Listener
```javascript
// resources/js/realtime.js
window.Echo.channel('absensi')
    .listen('QrCodeScanned', (e) => {
        // Show notification
        window.Filament.notifications.send({
            title: 'QR Code Scanned',
            body: `${e.murid_name} - ${e.status}`,
            icon: 'success',
        });
    });
```

## 📊 Polling Intervals

| Komponen | Interval | Keterangan |
|----------|----------|------------|
| StatsOverview | 30s | Dashboard stats |
| AbsensiChart | 60s | Chart kehadiran |
| RekapMingguan | 120s | Rekap mingguan |
| RekapBulanan | 120s | Rekap bulanan |
| RankingKehadiranKelas | 120s | Ranking kelas |
| RekapAbsensiKelas | 60s | Rekap per kelas |
| Absensi Table | 30s | Tabel absensi |
| Murid Table | 60s | Tabel murid |
| Guru Table | 60s | Tabel guru |
| Notifications | 30s | Database notifications |

## 🎨 Fitur Tambahan

### Live Updates
Filament panel sudah dikonfigurasi dengan `->liveUpdates()` untuk:
- Auto-refresh components saat ada perubahan
- Sync data antar tabs/windows
- Real-time collaboration

### SPA Mode
- Navigasi cepat tanpa reload
- Smooth transitions
- Better UX

## 🐛 Troubleshooting

### Reverb tidak connect?
1. Pastikan Reverb server running: `php artisan reverb:start`
2. Check port 8080 tidak dipakai aplikasi lain
3. Verify `.env` configuration

### Events tidak trigger?
1. Pastikan queue worker running: `php artisan queue:work`
2. Check `BROADCAST_CONNECTION=reverb` di `.env`
3. Clear config cache: `php artisan config:clear`

### Notifications tidak muncul?
1. Build assets: `npm run dev` atau `npm run build`
2. Clear browser cache
3. Check browser console untuk errors

## 📝 Development Tips

### Testing Broadcasting
```bash
# Terminal 1: Reverb server
php artisan reverb:start

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Laravel server
php artisan serve

# Terminal 4: Vite dev server
npm run dev
```

### Production Deployment
```bash
# Build assets
npm run build

# Start Reverb as daemon (using supervisor/systemd)
php artisan reverb:start --host=0.0.0.0 --port=8080

# Start queue worker as daemon
php artisan queue:work --daemon
```

## 🔌 API Endpoints

### QR Code Scan
**POST** `/api/qr-scan`

**Request Body:**
```json
{
  "code": "QR_CODE_STRING"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Absensi berhasil dicatat",
  "data": {
    "murid": "Nama Murid",
    "kelas": "10 IPA",
    "status": "Hadir",
    "tanggal": "2025-12-06",
    "waktu": "14:30:45"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "QR Code tidak valid atau tidak aktif"
}
```

### Testing dengan cURL
```bash
curl -X POST http://localhost:8000/api/qr-scan \
  -H "Content-Type: application/json" \
  -d '{"code":"YOUR_QR_CODE"}'
```

## 🎯 Next Steps

Untuk meningkatkan real-time features:
1. ✅ Tambah event untuk QR code scanning
2. ✅ Real-time notifications untuk admin
3. ✅ API endpoint untuk QR scan
4. ⏳ Private channels untuk user-specific updates
5. ⏳ Presence channels untuk "who's online"
6. ⏳ Real-time chat/messaging

## 📚 Resources

- [Laravel Broadcasting](https://laravel.com/docs/11.x/broadcasting)
- [Laravel Reverb](https://laravel.com/docs/11.x/reverb)
- [Filament Live Updates](https://filamentphp.com/docs/3.x/panels/configuration#live-updates)
