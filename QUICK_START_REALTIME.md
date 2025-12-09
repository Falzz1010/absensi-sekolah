# 🚀 Quick Start - Real-Time Features

## Langkah Cepat Menjalankan Fitur Real-Time

### Opsi 1: Menggunakan Batch Script (Recommended)
Jalankan semua service sekaligus:
```bash
start-realtime.bat
```

Script ini akan otomatis membuka 4 terminal untuk:
1. Laravel Reverb Server (WebSocket)
2. Queue Worker (untuk broadcasting)
3. Laravel Development Server
4. Vite Dev Server (untuk hot reload)

### Opsi 2: Manual (4 Terminal)

#### Terminal 1: Reverb Server
```bash
php artisan reverb:start
```
Output: `Reverb server started on http://0.0.0.0:8080`

#### Terminal 2: Queue Worker
```bash
php artisan queue:work
```
Output: `Processing jobs...`

#### Terminal 3: Laravel Server
```bash
php artisan serve
```
Output: `Server started on http://localhost:8000`

#### Terminal 4: Vite Dev
```bash
npm run dev
```
Output: `VITE ready in XXX ms`

## ✅ Verifikasi

### 1. Check Reverb Server
Buka browser: `http://localhost:8080`
Seharusnya muncul: `OK` atau Reverb status page

### 2. Check Laravel App
Buka browser: `http://localhost:8000/admin`
Login dengan akun admin

### 3. Test Real-Time Features

#### A. Dashboard Auto-Refresh
1. Buka dashboard
2. Buka tab baru, buat absensi baru
3. Kembali ke dashboard pertama
4. Dashboard akan auto-refresh dalam 30 detik

#### B. Table Auto-Refresh
1. Buka halaman Absensi
2. Buka tab baru, edit/tambah absensi
3. Kembali ke tab pertama
4. Table akan auto-refresh dalam 30 detik

#### C. Real-Time Notifications (QR Scan)
1. Buka dashboard
2. Scan QR code via API:
```bash
curl -X POST http://localhost:8000/api/qr-scan \
  -H "Content-Type: application/json" \
  -d '{"code":"YOUR_QR_CODE"}'
```
3. Notifikasi akan muncul real-time di dashboard

## 🔧 Troubleshooting

### Port sudah digunakan?

**Reverb (8080):**
```bash
# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F
```

**Laravel (8000):**
```bash
# Gunakan port lain
php artisan serve --port=8001
```

### Queue tidak jalan?
```bash
# Restart queue worker
php artisan queue:restart
php artisan queue:work
```

### Assets tidak update?
```bash
# Clear cache dan rebuild
npm run build
php artisan config:clear
php artisan view:clear
```

### Broadcasting tidak connect?
1. Check `.env`:
```env
BROADCAST_CONNECTION=reverb
REVERB_HOST="localhost"
REVERB_PORT=8080
```

2. Clear config:
```bash
php artisan config:clear
```

3. Restart Reverb:
```bash
# Stop (Ctrl+C) dan start lagi
php artisan reverb:start
```

## 📊 Monitoring

### Check Queue Jobs
```bash
php artisan queue:monitor
```

### Check Reverb Connections
```bash
# Lihat log di terminal Reverb
# Setiap koneksi baru akan muncul log
```

### Browser Console
Buka Developer Tools (F12) → Console
Cari log:
- `Echo connected`
- `Absensi created: ...`
- `Absensi updated: ...`

## 🎯 Testing Real-Time

### Test 1: Multi-Tab Sync
1. Buka 2 tab browser
2. Login di kedua tab
3. Di tab 1: buat absensi baru
4. Di tab 2: lihat auto-refresh

### Test 2: QR Code Scan
1. Buka dashboard
2. Gunakan Postman/cURL untuk scan QR:
```bash
POST http://localhost:8000/api/qr-scan
Body: {"code": "QR_CODE_STRING"}
```
3. Lihat notifikasi muncul real-time

### Test 3: Widget Auto-Refresh
1. Buka dashboard
2. Tunggu 30 detik
3. Stats akan auto-refresh
4. Check browser console untuk log polling

## 📝 Production Deployment

### 1. Build Assets
```bash
npm run build
```

### 2. Setup Supervisor (Linux)
```ini
[program:reverb]
command=php /path/to/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true

[program:queue]
command=php /path/to/artisan queue:work --tries=3
autostart=true
autorestart=true
```

### 3. Nginx Configuration
```nginx
location /app {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
}
```

### 4. Environment Variables
```env
BROADCAST_CONNECTION=reverb
REVERB_HOST="your-domain.com"
REVERB_PORT=8080
REVERB_SCHEME=https
```

## 🎉 Selesai!

Sekarang aplikasi sudah real-time:
- ✅ Dashboard auto-refresh
- ✅ Tables auto-refresh
- ✅ Real-time notifications
- ✅ QR scan dengan broadcasting
- ✅ Multi-tab sync

Untuk detail lebih lanjut, lihat: `FITUR_REALTIME.md`
