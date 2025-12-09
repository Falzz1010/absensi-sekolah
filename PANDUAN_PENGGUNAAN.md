# Panduan Penggunaan Sistem Absensi Sekolah

## 🚀 Fitur Lengkap

### 1. **Dashboard**
- Statistik real-time (Total Murid, Total Guru, Kehadiran Hari Ini)
- Chart kehadiran 7 hari terakhir
- Rekap kehadiran per kelas hari ini

### 2. **Input Absensi Kelas** (Fitur Baru!)
- Input absensi untuk seluruh kelas sekaligus
- Pilih kelas dan tanggal
- Otomatis load semua murid di kelas tersebut
- Default status "Hadir" untuk mempercepat input
- Lokasi: Menu **Akademik > Input Absensi Kelas**

### 3. **Manajemen Absensi**
- Input absensi per murid
- Filter berdasarkan kelas, status, dan tanggal
- Edit dan hapus data absensi
- Lokasi: Menu **Akademik > Absensi**

### 4. **Data Murid**
- Tambah, edit, hapus data murid
- Filter berdasarkan kelas
- Status aktif/non-aktif
- Lokasi: Menu **Akademik > Data Murid** (Admin only)

### 5. **Data Guru**
- Manajemen data guru
- Mata pelajaran dan kelas yang diajar
- Lokasi: Menu **Akademik > Data Guru** (Admin only)

### 6. **Jadwal Pelajaran**
- Buat dan kelola jadwal pelajaran
- Per hari, jam, kelas, dan guru
- Lokasi: Menu **Akademik > Jadwal Pelajaran**

### 7. **Laporan Kehadiran**
- Export ke Excel
- Filter berdasarkan tanggal, kelas, dan status
- Bulk export data absensi
- Lokasi: Menu **Laporan > Laporan Kehadiran**

### 8. **Manajemen User**
- Kelola user dan role (Admin, Guru, Murid)
- Assign role ke user
- Lokasi: Menu **Manajemen User > Users** (Admin only)

## 👥 Role & Akses

### Admin
- **Akses Penuh** ke semua fitur
- Dapat mengelola User, Guru, Murid
- Dapat melihat dan mengelola Absensi, Jadwal, Laporan

### Guru
- Input Absensi (per murid atau per kelas)
- Lihat dan kelola Jadwal
- Lihat dan export Laporan Kehadiran
- Lihat Dashboard

### Murid
- Saat ini belum ada akses ke admin panel
- (Bisa dikembangkan untuk portal murid terpisah)

## 🔐 Akun Default

### Admin
- Email: `admin@example.com`
- Password: `password`

### Guru
- Email: `guru@example.com`
- Password: `password`

### Murid
- Email: `murid@example.com`
- Password: `password`

## 📊 Data Dummy

Sistem sudah dilengkapi dengan data dummy:
- ✅ 3 User (Admin, Guru, Murid)
- ✅ 6 Guru dengan berbagai mata pelajaran
- ✅ 22 Murid dari berbagai kelas (10-12, IPA/IPS)
- ✅ 19 Jadwal pelajaran (Senin-Jumat)
- ✅ Data absensi 7 hari terakhir untuk semua murid

## 🎨 UI/UX Modern

- ✨ Design minimalis dengan Tailwind CSS
- 🎨 Color scheme Amber/Kuning yang warm
- 📱 Responsive design
- 🔄 Smooth animations
- 📊 Interactive charts dengan Chart.js
- 🎯 Icon modern dari Heroicons

## 💡 Tips Penggunaan

1. **Input Absensi Cepat**: Gunakan fitur "Input Absensi Kelas" untuk input absensi seluruh kelas sekaligus
2. **Export Laporan**: Pilih data yang ingin di-export, lalu klik tombol "Export Excel"
3. **Filter Data**: Gunakan filter untuk mempermudah pencarian data
4. **Dashboard**: Pantau statistik kehadiran real-time di dashboard

## 🔧 Maintenance

### Reset Database dengan Data Dummy
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan optimize:clear
```

### Rebuild Assets
```bash
npm run build
```

## 📝 Catatan

- Pastikan server Laravel dan Vite berjalan
- Data absensi otomatis ter-update di dashboard
- Semua perubahan langsung tersimpan ke database
