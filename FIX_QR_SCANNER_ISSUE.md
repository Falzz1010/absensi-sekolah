# Fix: QR Scanner Tidak Berfungsi

## Masalah
1. Kamera tidak bisa scan QR code
2. Upload gambar QR code tidak berfungsi

## Penyebab Umum

### 1. Browser Tidak Izinkan Akses Kamera
**Gejala**: Muncul error "Akses Kamera Ditolak"

**Solusi**:
- Klik icon kamera/lock di address bar browser
- Pilih "Allow" untuk akses kamera
- Refresh halaman
- Klik "Mulai Scan" lagi

### 2. HTTPS Required
**Gejala**: Kamera tidak muncul sama sekali

**Penyebab**: Browser modern memerlukan HTTPS untuk akses kamera (kecuali localhost)

**Solusi**:
- Jika development: Gunakan `http://localhost` atau `http://127.0.0.1`
- Jika production: Gunakan HTTPS (SSL certificate)

### 3. Library CDN Tidak Ter-load
**Gejala**: Error di console browser "Html5Qrcode is not defined"

**Solusi**:
- Buka browser console (F12)
- Cek apakah ada error loading script
- Pastikan koneksi internet stabil
- Coba refresh halaman (Ctrl+F5)

### 4. CSRF Token Missing
**Gejala**: Upload gambar gagal dengan error 419

**Solusi**:
- Pastikan ada `<meta name="csrf-token">` di layout
- Cek di browser console apakah CSRF token ada

## Cara Test

### Test 1: Cek Browser Support
```javascript
// Buka browser console (F12) dan jalankan:
navigator.mediaDevices.getUserMedia({ video: true })
  .then(() => console.log('✓ Kamera tersedia'))
  .catch(err => console.error('✗ Kamera error:', err));
```

### Test 2: Cek Library Loaded
```javascript
// Buka browser console (F12) dan jalankan:
console.log(typeof Html5Qrcode);
// Seharusnya output: "function"
```

### Test 3: Cek API Endpoint
```bash
# Test dari command line
curl -X POST http://localhost/api/qr-scan \
  -H "Content-Type: application/json" \
  -d '{"code":"test123"}'
```

### Test 4: Cek CSRF Token
```javascript
// Buka browser console (F12) dan jalankan:
console.log(document.querySelector('meta[name="csrf-token"]').content);
// Seharusnya output: token string
```

## Solusi Alternatif

### Jika Kamera Tidak Berfungsi:

**Gunakan Upload Gambar**:
1. Screenshot QR code atau ambil foto QR code
2. Di halaman QR Scan, klik "Pilih File Gambar"
3. Pilih gambar QR code
4. Sistem akan otomatis scan dari gambar

### Jika Upload Juga Tidak Berfungsi:

**Gunakan Manual Attendance**:
1. Buka menu "Manual Attendance"
2. Pilih tanggal
3. Pilih status kehadiran
4. Submit

## Troubleshooting Step-by-Step

### Step 1: Cek Browser Console
1. Buka halaman QR Scan
2. Tekan F12 untuk buka Developer Tools
3. Pilih tab "Console"
4. Klik "Mulai Scan"
5. Lihat apakah ada error di console

**Error Umum**:
- `NotAllowedError` → User tidak izinkan akses kamera
- `NotFoundError` → Kamera tidak ditemukan
- `NotReadableError` → Kamera sedang digunakan aplikasi lain
- `Html5Qrcode is not defined` → Library tidak ter-load

### Step 2: Cek Network Tab
1. Buka Developer Tools (F12)
2. Pilih tab "Network"
3. Upload gambar QR code
4. Lihat request ke `/api/qr-scan`
5. Cek response status:
   - 200 = Success
   - 404 = QR code tidak valid
   - 419 = CSRF token error
   - 500 = Server error

### Step 3: Cek Permission
1. Klik icon di address bar (kiri URL)
2. Cek "Camera" permission
3. Pastikan set ke "Allow"
4. Refresh halaman

### Step 4: Test dengan Browser Lain
- Chrome (recommended)
- Firefox
- Safari (iOS)
- Edge

**Catatan**: Beberapa browser mobile mungkin tidak support kamera API

## Fix untuk Development

### Jika Menggunakan IP Address (bukan localhost):

Browser tidak akan izinkan akses kamera via HTTP dengan IP address. Solusi:

**Opsi 1: Gunakan localhost**
```
http://localhost/student/qr-scan
```

**Opsi 2: Setup HTTPS untuk development**
```bash
# Generate self-signed certificate
php artisan serve --host=0.0.0.0 --port=8000

# Atau gunakan Laravel Valet (Mac)
valet secure

# Atau gunakan ngrok untuk tunnel HTTPS
ngrok http 80
```

**Opsi 3: Chrome Flag (Temporary)**
1. Buka `chrome://flags/#unsafely-treat-insecure-origin-as-secure`
2. Tambahkan URL Anda (contoh: `http://192.168.1.100`)
3. Restart Chrome

## Fix untuk Production

### Setup HTTPS (Required)
```bash
# Install Certbot (Let's Encrypt)
sudo apt-get install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renew
sudo certbot renew --dry-run
```

## Verifikasi QR Code

### Cek QR Code di Database
```bash
php artisan tinker
>>> App\Models\QrCode::where('is_active', true)->get(['id', 'code', 'berlaku_dari', 'berlaku_sampai']);
```

### Generate QR Code Baru
```bash
php artisan db:seed --class=QrCodeSeeder
```

### Assign QR Code ke Murid
```bash
php artisan tinker
>>> $murid = App\Models\Murid::find(1);
>>> $qrCode = App\Models\QrCode::first();
>>> $murid->qr_code_id = $qrCode->id;
>>> $murid->save();
```

## Test Manual QR Scan

### Test dengan QR Code Generator Online:
1. Buka https://www.qr-code-generator.com/
2. Generate QR code dengan text: kode dari database (contoh: "QR-2024-001")
3. Print atau tampilkan di layar
4. Scan dengan aplikasi

### Test dengan Gambar:
1. Download QR code image
2. Upload via "Pilih File Gambar"
3. Cek response di console

## Checklist Troubleshooting

- [ ] Browser support camera API
- [ ] Permission camera allowed
- [ ] HTTPS atau localhost
- [ ] Library html5-qrcode loaded
- [ ] CSRF token exists
- [ ] API endpoint `/api/qr-scan` accessible
- [ ] QR code exists in database
- [ ] QR code is_active = true
- [ ] Murid has qr_code_id assigned
- [ ] Murid is_active = true

## Status Saat Ini

✅ **Backend**: API endpoint berfungsi
✅ **Frontend**: QR scan page ada
✅ **Library**: html5-qrcode CDN included
⚠️ **Permission**: Perlu user allow camera access
⚠️ **HTTPS**: Perlu HTTPS untuk production

## Rekomendasi

1. **Development**: Gunakan `http://localhost` atau `http://127.0.0.1`
2. **Production**: Setup HTTPS dengan SSL certificate
3. **Alternative**: Gunakan upload gambar jika kamera tidak berfungsi
4. **Fallback**: Gunakan Manual Attendance sebagai backup

## Error Messages dan Solusi

| Error | Penyebab | Solusi |
|-------|----------|--------|
| "Akses Kamera Ditolak" | User tidak izinkan | Allow permission di browser |
| "Kamera tidak ditemukan" | Device tidak punya kamera | Gunakan upload gambar |
| "Kamera sedang digunakan" | App lain pakai kamera | Tutup app lain |
| "QR Code tidak valid" | Code tidak ada di DB | Generate QR code baru |
| "QR Code sudah tidak aktif" | Expired | Update berlaku_sampai |
| "Koneksi gagal" | Network issue | Cek koneksi internet |
| "CSRF token mismatch" | Token expired | Refresh halaman |

## Dokumentasi Terkait

- FIX_QR_SCANNER_CAMERA_ISSUE.md
- FIX_QR_SCANNER_STATE.md
- FIX_QR_SCANNER_ERROR.md
- QR_CODE_FEATURE.md
