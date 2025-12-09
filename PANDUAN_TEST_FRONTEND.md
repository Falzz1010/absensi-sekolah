# 🖥️ Panduan Test Frontend - Manual Testing

## ⚠️ PENTING: Test Manual Required

Saya sudah test backend (database, routes, code), tapi untuk memastikan frontend benar-benar muncul dan berfungsi, **kamu perlu test manual di browser**.

---

## 🚀 Langkah-Langkah Test

### Step 1: Start Services

Buka 4 terminal dan jalankan:

**Terminal 1: Laravel Server**
```bash
php artisan serve
```
Output yang diharapkan:
```
Server started on http://localhost:8000
```

**Terminal 2: Reverb Server (untuk real-time)**
```bash
php artisan reverb:start
```
Output yang diharapkan:
```
Reverb server started on http://0.0.0.0:8080
```

**Terminal 3: Queue Worker (untuk broadcasting)**
```bash
php artisan queue:work
```
Output yang diharapkan:
```
Processing jobs...
```

**Terminal 4: Vite Dev (optional, untuk hot reload)**
```bash
npm run dev
```
Output yang diharapkan:
```
VITE ready in XXX ms
```

**ATAU gunakan batch script:**
```bash
start-realtime.bat
```

---

### Step 2: Test Login Page

1. **Buka browser:** `http://localhost:8000/admin`

2. **Cek yang harus muncul:**
   - ✅ Form login
   - ✅ Field email
   - ✅ Field password
   - ✅ Button "Sign in"
   - ✅ Logo/Brand "Absensi Sekolah"
   - ✅ Styling Filament (warna amber/kuning)

3. **Login dengan:**
   - Email: `admin@admin.com`
   - Password: `password`

4. **Jika berhasil:**
   - ✅ Redirect ke dashboard
   - ✅ Tidak ada error
   - ✅ Sidebar muncul

---

### Step 3: Test Dashboard

**URL:** `http://localhost:8000/admin`

**Cek yang harus muncul:**

1. **Sidebar Navigation:**
   - ✅ Dashboard
   - ✅ Akademik (group)
     - Absensi
     - Input Absensi Kelas
     - Jadwal Pelajaran
     - Data Murid
     - Data Guru
     - Manajemen Kelas
   - ✅ Laporan (group)
     - Dashboard Wali Kelas (jika wali kelas)
     - Dashboard Overview
     - Laporan Harian
     - Laporan Kehadiran
   - ✅ Manajemen User (group)
     - Users
   - ✅ Pengaturan (group)
     - Tahun Ajaran
     - Jam Pelajaran
     - QR Code Absensi
     - Hari Libur
     - Pengaturan Sekolah

2. **Dashboard Content:**
   - ✅ Stats cards (3 cards):
     - Total Murid
     - Total Guru
     - Kehadiran Hari Ini
   - ✅ Chart "Statistik Kehadiran 7 Hari Terakhir"
   - ✅ Widgets loading dengan benar
   - ✅ Tidak ada error di console

3. **Top Bar:**
   - ✅ Bell icon (notifications)
   - ✅ User menu (top right)
   - ✅ Logout option

---

### Step 4: Test Absensi Page

**URL:** `http://localhost:8000/admin/absensis`

**Cek yang harus muncul:**

1. **Table:**
   - ✅ Columns: Nama Murid, Kelas, Tanggal, Status, Keterangan
   - ✅ Data absensi muncul (154 records)
   - ✅ Pagination working
   - ✅ Search box working

2. **Filters:**
   - ✅ Filter Kelas
   - ✅ Filter Status
   - ✅ Filter Hari Ini (toggle)

3. **Actions:**
   - ✅ Button "New" (create absensi)
   - ✅ Edit icon per row
   - ✅ Delete icon per row

4. **Auto-Refresh:**
   - ✅ Wait 30 seconds
   - ✅ Table should refresh automatically
   - ✅ Check browser console: should see polling activity

---

### Step 5: Test Input Absensi Kelas

**URL:** `http://localhost:8000/admin/input-absensi-kelas`

**Cek yang harus muncul:**

1. **Form:**
   - ✅ Dropdown "Pilih Kelas"
   - ✅ DatePicker "Tanggal"
   - ✅ Default tanggal: hari ini

2. **Setelah pilih kelas:**
   - ✅ List murid muncul
   - ✅ Setiap murid punya dropdown status
   - ✅ Options: Hadir, Sakit, Izin, Alfa

3. **Submit:**
   - ✅ Button "Simpan Absensi"
   - ✅ Klik simpan
   - ✅ Notification muncul: "Absensi berhasil disimpan"
   - ✅ Form reset

---

### Step 6: Test Dashboard Wali Kelas

**URL:** `http://localhost:8000/admin/dashboard-wali-kelas`

**Note:** Hanya muncul jika login sebagai guru yang ditugaskan sebagai wali kelas.

**Cek yang harus muncul:**

1. **Filter:**
   - ✅ Dropdown Bulan
   - ✅ Dropdown Tahun

2. **Informasi Kelas:**
   - ✅ 5 cards: Nama Kelas, Tingkat, Jurusan, Jumlah Murid, Kapasitas

3. **Statistik Bulanan:**
   - ✅ 8 cards: Hari Kerja, Total Hadir, Sakit, Izin, Alfa, Rata-rata, Total Murid, Total Absensi

4. **Rekap Per Murid:**
   - ✅ Table dengan columns: No, Nama, Email, Hadir, Sakit, Izin, Alfa, Total, % Kehadiran
   - ✅ Color coding: Hijau (≥80%), Kuning (60-79%), Merah (<60%)
   - ✅ Sorted by persentase (terbaik dulu)

5. **Export Buttons:**
   - ✅ Button "Export Excel"
   - ✅ Button "Export PDF"

---

### Step 7: Test Laporan Harian

**URL:** `http://localhost:8000/admin/laporan-harian`

**Cek yang harus muncul:**

1. **Filter:**
   - ✅ DatePicker "Tanggal"
   - ✅ Dropdown "Filter Kelas"

2. **Summary Cards:**
   - ✅ 5 cards: Total Absensi, Hadir, Sakit, Izin, Alfa
   - ✅ Persentase kehadiran

3. **Detail Per Kelas:**
   - ✅ Table dengan breakdown per kelas
   - ✅ Persentase dengan color coding

4. **Live Update:**
   - ✅ Ubah filter
   - ✅ Data auto-update tanpa reload

---

### Step 8: Test QR Code

**URL:** `http://localhost:8000/admin/qr-codes`

**Cek yang harus muncul:**

1. **Table:**
   - ✅ Columns: Nama, Tipe, Kelas, Kode, Status, Berlaku Dari, Berlaku Sampai
   - ✅ 5 QR codes muncul

2. **Actions per row:**
   - ✅ Button "Lihat QR" (eye icon)
   - ✅ Button "Download" (download icon)
   - ✅ Edit icon
   - ✅ Delete icon

3. **Test View QR:**
   - ✅ Klik "Lihat QR"
   - ✅ Opens new tab
   - ✅ QR code image muncul (SVG format)

4. **Test Download QR:**
   - ✅ Klik "Download"
   - ✅ File downloaded (SVG format)
   - ✅ File bisa dibuka

---

### Step 9: Test Real-Time Notifications

**Persiapan:**
1. Pastikan Reverb server running
2. Pastikan Queue worker running
3. Buka browser console (F12)

**Test QR Scan Notification:**

1. **Buka dashboard di browser**

2. **Di terminal baru, test QR scan API:**
```bash
curl -X POST http://localhost:8000/api/qr-scan -H "Content-Type: application/json" -d "{\"code\":\"aobcmSePl8wDNRDv4QSiiN25cXnlJB7W\"}"
```

3. **Cek di browser:**
   - ✅ Toast notification muncul (top right)
   - ✅ Text: "QR Code Scanned"
   - ✅ Body: "Nama Murid - Hadir (Kelas)"
   - ✅ Auto-dismiss after 5 seconds

4. **Cek browser console:**
   - ✅ Should see: "Absensi created: ..."
   - ✅ No errors

---

### Step 10: Test Multi-Tab Sync

1. **Buka dashboard di 2 tabs**

2. **Di tab 1:**
   - Go to Absensi
   - Create new absensi

3. **Di tab 2:**
   - Stay on dashboard
   - Wait 30 seconds

4. **Expected:**
   - ✅ Tab 2 widgets auto-refresh
   - ✅ Stats updated
   - ✅ No manual refresh needed

---

## 🐛 Common Issues & Solutions

### Issue 1: Login page tidak muncul

**Symptoms:**
- Blank page
- Error 500
- "Connection refused"

**Solutions:**
```bash
# Check if server running
# Should see: Server started on http://localhost:8000

# If not, start server:
php artisan serve

# Clear cache:
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

### Issue 2: Dashboard muncul tapi widgets kosong

**Symptoms:**
- Sidebar OK
- Content area blank
- No widgets

**Solutions:**
```bash
# Check database:
php artisan tinker --execute="echo App\Models\Murid::count();"

# If 0, run seeder:
php artisan db:seed

# Clear cache:
php artisan optimize:clear
```

---

### Issue 3: Styling rusak / tidak ada warna

**Symptoms:**
- Layout berantakan
- No colors
- Plain HTML

**Solutions:**
```bash
# Rebuild assets:
npm run build

# Or for dev:
npm run dev

# Clear browser cache:
# Ctrl+Shift+R (Windows)
# Cmd+Shift+R (Mac)
```

---

### Issue 4: Real-time tidak working

**Symptoms:**
- No notifications
- No auto-refresh
- Console error: "Echo not defined"

**Solutions:**
```bash
# Check Reverb:
php artisan reverb:start

# Check Queue:
php artisan queue:work

# Check .env:
BROADCAST_CONNECTION=reverb

# Rebuild assets:
npm run build

# Check browser console for errors
```

---

### Issue 5: 404 Not Found pada menu tertentu

**Symptoms:**
- Menu muncul di sidebar
- Klik menu → 404

**Solutions:**
```bash
# Clear route cache:
php artisan route:clear

# Check if route exists:
php artisan route:list --path=admin

# Clear all cache:
php artisan optimize:clear
```

---

## ✅ Checklist Test Frontend

Print checklist ini dan centang saat test:

### Basic Access:
- [ ] Login page muncul
- [ ] Bisa login dengan admin@admin.com
- [ ] Dashboard muncul setelah login
- [ ] Sidebar navigation muncul
- [ ] Top bar muncul (bell icon, user menu)

### Dashboard:
- [ ] Stats cards muncul (3 cards)
- [ ] Chart muncul
- [ ] Widgets loading
- [ ] No errors di console

### Absensi:
- [ ] Table muncul dengan data
- [ ] Pagination working
- [ ] Search working
- [ ] Filters working
- [ ] Create button working
- [ ] Edit working
- [ ] Auto-refresh after 30s

### Input Absensi:
- [ ] Form muncul
- [ ] Dropdown kelas working
- [ ] Murid list muncul setelah pilih kelas
- [ ] Status dropdown working
- [ ] Submit working
- [ ] Notification muncul

### Dashboard Wali Kelas:
- [ ] Menu muncul (jika wali kelas)
- [ ] Filter working
- [ ] Info kelas muncul
- [ ] Statistik muncul
- [ ] Rekap table muncul
- [ ] Color coding working
- [ ] Export buttons muncul

### Laporan Harian:
- [ ] Filter working
- [ ] Summary cards muncul
- [ ] Detail table muncul
- [ ] Live update working

### QR Code:
- [ ] Table muncul
- [ ] View QR working
- [ ] Download QR working
- [ ] QR image muncul

### Real-Time:
- [ ] Reverb server running
- [ ] Queue worker running
- [ ] QR scan notification muncul
- [ ] Auto-refresh working
- [ ] Multi-tab sync working
- [ ] No console errors

### Performance:
- [ ] Page load < 3 seconds
- [ ] No lag saat navigasi
- [ ] SPA mode working (no full reload)
- [ ] Smooth transitions

### Mobile:
- [ ] Responsive di mobile
- [ ] Sidebar collapsible
- [ ] Table scrollable
- [ ] Forms usable

---

## 📸 Screenshot Checklist

Ambil screenshot untuk dokumentasi:

1. [ ] Login page
2. [ ] Dashboard (full view)
3. [ ] Absensi table
4. [ ] Input Absensi form
5. [ ] Dashboard Wali Kelas
6. [ ] Laporan Harian
7. [ ] QR Code view
8. [ ] Notification toast
9. [ ] Mobile view

---

## 🎯 Expected Results

Jika semua test passed:

✅ **Login:** Smooth, no errors  
✅ **Dashboard:** All widgets visible  
✅ **Navigation:** All menus accessible  
✅ **Forms:** All working  
✅ **Tables:** Data displayed, pagination working  
✅ **Real-time:** Notifications working  
✅ **Performance:** Fast, responsive  
✅ **Mobile:** Usable on small screens  

---

## 📝 Report Template

Setelah test, isi report ini:

```
FRONTEND TEST REPORT
Date: ___________
Tester: ___________

✅ PASSED / ❌ FAILED

1. Login: ___
2. Dashboard: ___
3. Absensi: ___
4. Input Absensi: ___
5. Dashboard Wali Kelas: ___
6. Laporan Harian: ___
7. QR Code: ___
8. Real-Time: ___
9. Performance: ___
10. Mobile: ___

Issues Found:
_________________________
_________________________

Overall Status: ___________
```

---

**PENTING:** Lakukan test ini untuk memastikan frontend benar-benar bisa diakses dan berfungsi dengan baik!

**Last Updated:** December 6, 2025  
**Status:** Ready for Manual Testing
