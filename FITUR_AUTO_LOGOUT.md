# Fitur Auto Logout

## Overview
Sistem auto logout otomatis mengeluarkan user dari aplikasi setelah tidak aktif dalam waktu tertentu untuk keamanan.

## Konfigurasi

### Timeout Duration
- **Default**: 30 menit tidak aktif
- **Peringatan**: 2 menit sebelum logout
- **Lokasi konfigurasi**: `resources/js/auto-logout.js`

```javascript
const TIMEOUT_DURATION = 30 * 60 * 1000; // 30 menit
const WARNING_DURATION = 2 * 60 * 1000;  // Peringatan 2 menit sebelum
```

### Session Lifetime
Session Laravel juga dikonfigurasi di `config/session.php`:
```php
'lifetime' => 120, // 120 menit (2 jam)
```

## Cara Kerja

### 1. Deteksi Aktivitas User
Sistem mendeteksi aktivitas user melalui event:
- Mouse movement
- Mouse click
- Keyboard input
- Scroll
- Touch (mobile)

### 2. Timer Reset
Setiap kali ada aktivitas, timer akan direset ke 0.

### 3. Peringatan
**2 menit sebelum logout**, sistem menampilkan notifikasi:
- Posisi: Kanan atas layar
- Warna: Amber/Orange gradient
- Countdown: Menghitung mundur waktu tersisa
- Action: User bisa klik di mana saja untuk tetap login

### 4. Auto Logout
Jika tidak ada aktivitas setelah 30 menit:
- Tampil overlay "Sesi Berakhir"
- Otomatis logout dalam 2 detik
- Redirect ke halaman login

## Implementasi

### File yang Terlibat

1. **JavaScript Client-side**
   - `resources/js/auto-logout.js` - Source file
   - `public/js/auto-logout.js` - Published file

2. **Middleware (Optional)**
   - `app/Http/Middleware/AutoLogout.php` - Server-side validation

3. **Panel Providers**
   - `app/Providers/Filament/AdminPanelProvider.php`
   - `app/Providers/Filament/StudentPanelProvider.php`

### Integrasi ke Panel

Script auto-logout ditambahkan via `renderHook`:

```php
->renderHook(
    'panels::body.end',
    fn (): string => '<script src="' . asset('js/auto-logout.js') . '"></script>'
)
```

## Fitur UI

### Warning Notification
- **Design**: Modern card dengan gradient amber
- **Icon**: Warning icon SVG
- **Countdown**: Real-time countdown timer
- **Close button**: User bisa close notifikasi
- **Animation**: Smooth slide-in dari kanan

### Logout Overlay
- **Background**: Dark overlay (rgba)
- **Modal**: White card dengan shadow
- **Icon**: Clock icon
- **Message**: "Sesi Berakhir"
- **Info**: Penjelasan logout otomatis

## Keamanan

### Manfaat
✅ Mencegah akses tidak sah jika user lupa logout
✅ Melindungi data sensitif di komputer publik
✅ Compliance dengan standar keamanan
✅ Mengurangi risiko session hijacking

### Best Practices
- Timeout 30 menit sesuai standar industri
- Peringatan 2 menit memberi waktu user bereaksi
- Session server-side juga expire (double protection)
- Clear session data saat logout

## Customization

### Mengubah Timeout Duration

Edit `resources/js/auto-logout.js`:

```javascript
// Ubah dari 30 menit ke 15 menit
const TIMEOUT_DURATION = 15 * 60 * 1000;

// Ubah peringatan dari 2 menit ke 1 menit
const WARNING_DURATION = 1 * 60 * 1000;
```

Setelah edit, copy ke public:
```bash
copy resources\js\auto-logout.js public\js\auto-logout.js
```

### Mengubah Warna Warning

Edit inline style di `auto-logout.js`:

```javascript
// Ubah dari amber ke red
background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
```

### Disable Auto Logout

Hapus renderHook dari panel provider:

```php
// Comment atau hapus baris ini
// ->renderHook('panels::body.end', ...)
```

## Testing

### Manual Test
1. Login ke sistem
2. Tunggu 28 menit (atau ubah timeout jadi lebih pendek untuk testing)
3. Warning notification muncul
4. Tunggu 2 menit lagi
5. Otomatis logout dan redirect ke login

### Quick Test (Development)
Ubah timeout jadi 1 menit untuk testing cepat:

```javascript
const TIMEOUT_DURATION = 1 * 60 * 1000; // 1 menit
const WARNING_DURATION = 10 * 1000;     // 10 detik
```

## Troubleshooting

### Script tidak load
**Problem**: Auto-logout tidak berjalan

**Solution**:
1. Pastikan file ada di `public/js/auto-logout.js`
2. Clear browser cache
3. Check console browser untuk error
4. Pastikan renderHook sudah ditambahkan

### Warning tidak muncul
**Problem**: Tidak ada notifikasi peringatan

**Solution**:
1. Check console browser
2. Pastikan tidak ada CSS conflict
3. Verify z-index cukup tinggi (99999)

### Logout terlalu cepat
**Problem**: User logout sebelum 30 menit

**Solution**:
1. Check `TIMEOUT_DURATION` di auto-logout.js
2. Pastikan event listeners berjalan
3. Test dengan console.log di resetTimer()

## Browser Compatibility

✅ Chrome/Edge (Chromium)
✅ Firefox
✅ Safari
✅ Mobile browsers (iOS/Android)

## Performance

- **Overhead**: Minimal (~1KB JavaScript)
- **CPU**: Negligible (passive event listeners)
- **Memory**: < 1MB
- **Network**: 1 request saat load page

## Status

✅ **Implemented**: Auto logout dengan warning
✅ **Tested**: Manual testing passed
✅ **Integrated**: Admin & Student panels
✅ **Documented**: Complete documentation

## Next Steps (Optional)

- [ ] Add user preference untuk custom timeout
- [ ] Log auto-logout events ke database
- [ ] Email notification saat auto-logout
- [ ] Remember me option untuk extend session
- [ ] Admin dashboard untuk monitor active sessions
