# ✅ Checklist Test Cepat - 5 Menit

## 🚀 Start Services (1 menit)

```bash
# Jalankan ini:
start-realtime.bat

# ATAU manual:
php artisan serve          # Terminal 1
php artisan reverb:start   # Terminal 2
php artisan queue:work     # Terminal 3
```

---

## 🧪 Test Cepat (4 menit)

### 1. ✅ Login (30 detik)
- [ ] Buka: `http://localhost:8000/admin`
- [ ] Login: `admin@admin.com` / `password`
- [ ] Dashboard muncul?

### 2. ✅ Dashboard (30 detik)
- [ ] 3 stats cards muncul?
- [ ] Chart muncul?
- [ ] Sidebar ada menu?

### 3. ✅ Absensi (1 menit)
- [ ] Klik menu "Absensi"
- [ ] Table muncul dengan data?
- [ ] Klik "New" → Form muncul?
- [ ] Ada field "Keterangan"?

### 4. ✅ Input Absensi Kelas (1 menit)
- [ ] Klik menu "Input Absensi Kelas"
- [ ] Pilih kelas → Murid list muncul?
- [ ] Bisa ubah status?

### 5. ✅ QR Code (30 detik)
- [ ] Klik menu "QR Code Absensi"
- [ ] Table muncul?
- [ ] Klik "Lihat QR" → QR muncul?

### 6. ✅ Laporan Harian (30 detik)
- [ ] Klik menu "Laporan Harian"
- [ ] Summary cards muncul?
- [ ] Table detail muncul?

---

## 🎯 Quick Test Result

**Jika semua ✅:**
→ Frontend working! 🎉

**Jika ada ❌:**
→ Lihat `PANDUAN_TEST_FRONTEND.md` untuk troubleshooting

---

## 🔥 Bonus: Test Real-Time (optional)

```bash
# Di terminal baru:
curl -X POST http://localhost:8000/api/qr-scan -H "Content-Type: application/json" -d "{\"code\":\"aobcmSePl8wDNRDv4QSiiN25cXnlJB7W\"}"
```

- [ ] Notification muncul di dashboard?

---

**Total Time:** 5 menit  
**Status:** ___________
