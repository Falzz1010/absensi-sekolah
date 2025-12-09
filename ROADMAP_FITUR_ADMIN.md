# 🎓 Roadmap Fitur Admin - Sistem Absensi Sekolah

## ✅ SUDAH SELESAI (Current Version)

### A. Manajemen User
- ✅ CRUD User dengan role (Admin, Guru, Murid)
- ✅ CRUD Guru
- ✅ CRUD Murid
- ✅ Assign role ke user

### B. Manajemen Absensi
- ✅ Input absensi per murid
- ✅ Input absensi per kelas (bulk)
- ✅ Melihat seluruh absensi
- ✅ Filter berdasarkan tanggal, kelas, status
- ✅ Edit & hapus absensi

### C. Laporan
- ✅ Laporan kehadiran dengan filter
- ✅ Export ke Excel
- ✅ Rekap kehadiran per kelas hari ini

### D. Dashboard
- ✅ Grafik kehadiran 7 hari terakhir
- ✅ Statistik total murid, guru, kehadiran
- ✅ Rekap per kelas hari ini
- ✅ Chart dengan warna per status

### E. Jadwal
- ✅ CRUD Jadwal pelajaran
- ✅ Assign guru ke jadwal

---

## 🚧 DALAM PENGEMBANGAN (Next Sprint)

### 1. Manajemen Kelas (Priority: HIGH)
- 🔄 Model & Migration sudah dibuat
- ⏳ CRUD Kelas (X IPA 1, XI IPS 2, dst)
- ⏳ Assign wali kelas
- ⏳ Kapasitas kelas
- ⏳ Status aktif/non-aktif

### 2. Tahun Ajaran & Semester (Priority: HIGH)
- 🔄 Model & Migration sudah dibuat
- ⏳ CRUD Tahun Ajaran
- ⏳ Setting semester (Ganjil/Genap)
- ⏳ Periode aktif
- ⏳ Toggle tahun ajaran aktif

### 3. Settings Sekolah (Priority: MEDIUM)
- 🔄 Model & Migration sudah dibuat
- ⏳ Jam pelajaran (mulai & selesai)
- ⏳ Batas waktu absensi
- ⏳ Toleransi keterlambatan
- ⏳ Nama sekolah & logo

---

## 📋 BACKLOG (Future Features)

### A. Laporan Lanjutan
- ⏳ Laporan absensi per hari (detail)
- ⏳ Laporan absensi per guru
- ⏳ Rekap bulanan
- ⏳ Export PDF (selain Excel)
- ⏳ Grafik persentase kehadiran
- ⏳ Ranking kehadiran kelas

### B. Import/Export
- ⏳ Import data guru via Excel
- ⏳ Import data murid via Excel
- ⏳ Template Excel untuk import
- ⏳ Validasi data import

### C. Kalender & Libur
- ⏳ Kalender akademik
- ⏳ Hari libur sekolah
- ⏳ Hari libur nasional
- ⏳ Event sekolah

### D. QR Code Absensi
- ⏳ Generate QR Code per kelas
- ⏳ QR Code global sekolah
- ⏳ Scan QR untuk absensi
- ⏳ Validasi lokasi GPS

### E. Reset Password
- ⏳ Admin reset password user
- ⏳ Kirim email reset password
- ⏳ User change password sendiri

### F. Dashboard Advanced
- ⏳ Statistik mingguan detail
- ⏳ Statistik bulanan detail
- ⏳ Perbandingan antar kelas
- ⏳ Trend kehadiran
- ⏳ Alert murid sering alfa

### G. Notifikasi
- ⏳ Notifikasi ke orang tua (WhatsApp/Email)
- ⏳ Notifikasi murid alfa
- ⏳ Reminder absensi untuk guru

---

## 🎯 PRIORITAS IMPLEMENTASI

### Sprint 1 (Sekarang - Selesai)
✅ Basic CRUD semua entitas
✅ Absensi per murid & per kelas
✅ Dashboard dengan chart
✅ Export Excel
✅ Role-based access

### Sprint 2 (Next - Estimasi 2-3 hari)
1. ⏳ Lengkapi CRUD Kelas dengan wali kelas
2. ⏳ CRUD Tahun Ajaran
3. ⏳ Settings dasar (jam, batas waktu)
4. ⏳ Laporan per hari & per guru
5. ⏳ Rekap bulanan

### Sprint 3 (Future - Estimasi 3-4 hari)
1. ⏳ Import Excel (Guru & Murid)
2. ⏳ Kalender & Libur
3. ⏳ Dashboard advanced dengan lebih banyak chart
4. ⏳ Export PDF
5. ⏳ Reset password

### Sprint 4 (Advanced - Estimasi 5-7 hari)
1. ⏳ QR Code absensi
2. ⏳ GPS validation
3. ⏳ Notifikasi WhatsApp/Email
4. ⏳ Mobile app integration
5. ⏳ API untuk mobile

---

## 📊 PROGRESS TRACKING

**Total Fitur Planned:** ~50 fitur
**Sudah Selesai:** ~20 fitur (40%)
**Dalam Pengembangan:** ~5 fitur (10%)
**Backlog:** ~25 fitur (50%)

---

## 💡 CATATAN TEKNIS

### Tech Stack
- Laravel 11
- Filament 3
- Tailwind CSS
- Chart.js
- Maatwebsite Excel
- SQLite (bisa diganti MySQL)

### Optimasi yang Sudah Dilakukan
- ✅ Role-based access control
- ✅ Eager loading untuk performa
- ✅ Index database
- ✅ Caching query
- ✅ Responsive design
- ✅ Modern UI/UX

### Yang Perlu Dioptimasi
- ⏳ Queue untuk export besar
- ⏳ Cache untuk dashboard
- ⏳ Pagination untuk data besar
- ⏳ Background job untuk notifikasi

---

## 🚀 CARA KONTRIBUSI

Jika ingin menambah fitur:
1. Pilih fitur dari backlog
2. Buat branch baru
3. Implementasi dengan test
4. Submit PR dengan dokumentasi
5. Review & merge

---

**Last Updated:** 6 Desember 2025
**Version:** 1.0.0
**Status:** Production Ready (Basic Features)
