
# 🎓 Sistem Absensi Sekolah

Aplikasi manajemen absensi sekolah berbasis web dengan fitur **real-time** menggunakan Laravel 12, Filament 3, dan teknologi modern.

## ✨ Fitur Utama

### 📊 Dashboard Real-Time
- **Welcome Widget** dengan greeting dinamis (Pagi/Siang/Sore/Malam)
- Auto-refresh statistics setiap 30 detik
- Chart kehadiran 7 hari terakhir
- Rekap mingguan dan bulanan
- Ranking kehadiran per kelas
- Widget verifikasi absensi

### 👥 Manajemen User
- **Multi-role:** Admin, Guru, Murid
- **Auto-create user** saat tambah Murid/Guru
- Import Excel untuk Murid & Guru
- Export template CSV
- Role-based permissions dengan Spatie
- Profile management dengan foto

### ✅ Sistem Absensi
- **Input absensi per kelas** (bulk)
- **QR Code** untuk setiap murid
- **Scan QR** untuk absensi otomatis (HTML5)
- **Double Verification** (Murid scan → Admin/Guru verifikasi)
- **Absensi Manual** untuk siswa tanpa QR
- **Pengajuan Izin/Sakit** dengan upload bukti
- Status: Hadir, Sakit, Izin, Alfa
- Real-time notifications

### 🎓 Portal Siswa
- **Dashboard khusus** untuk siswa
- **QR Scanner** dengan kamera
- **Riwayat absensi** 30 hari
- **Jadwal hari ini**
- **Pengajuan izin/sakit** dengan upload dokumen
- **Notifikasi real-time**
- **Profile management**

### 👨‍🏫 Fitur Guru
- **Dashboard Wali Kelas** dengan statistik kelas
- **Input absensi kelas** bulk
- **Verifikasi pengajuan izin**
- **Laporan harian** per kelas
- **Monitoring kehadiran** siswa

### 🔔 Sistem Notifikasi
- **Real-time notifications** dengan polling 30s
- **Notifikasi pengajuan izin** ke Admin/Guru
- **Notifikasi hari libur** ke semua siswa
- **Pengumuman penting** dengan prioritas
- **SweetAlert2** untuk alert yang menarik
- Bell icon dengan badge counter

### 📈 Laporan & Analitik
- Laporan kehadiran per periode
- Export Excel & PDF
- Filter by kelas, tanggal, guru, status
- Statistik lengkap per siswa
- Grafik kehadiran
- Ranking kelas

### ⚙️ Pengaturan Sekolah
- Tahun ajaran
- Kelas & Wali Kelas
- Jam pelajaran
- Jadwal pelajaran
- Hari libur
- QR Code management
- Pengumuman

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
php artisan migrate --seed
```

### 4. Build Assets
```bash
npm run build
```

### 5. Start Development Server
```bash
php artisan serve
```

### 6. Login
**Admin Panel:**
- URL: `http://127.0.0.1:8000/admin`
- Email: `admin@example.com`
- Password: `password`

**Student Portal:**
- URL: `http://127.0.0.1:8000/student`
- Email: `murid@example.com`
- Password: `password`

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│         Laravel 12 Application          │
│  ┌─────────────┐    ┌─────────────┐   │
│  │   Admin     │    │   Student   │   │
│  │   Panel     │    │   Portal    │   │
│  │ (Filament)  │    │ (Filament)  │   │
│  └─────────────┘    └─────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐  │
│  │      Livewire Components        │  │
│  │  (Real-time Polling 30-120s)    │  │
│  └─────────────────────────────────┘  │
│                                         │
│  ┌─────────────────────────────────┐  │
│  │    Observers & Notifications    │  │
│  │  (Auto-send on create/update)   │  │
│  └─────────────────────────────────┘  │
│                                         │
│  ┌─────────────────────────────────┐  │
│  │      SQLite Database            │  │
│  │  (Users, Absensi, Notifications)│  │
│  └─────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

## 🔌 API Endpoints

### QR Code Scan
```bash
POST /api/qr-scan
Content-Type: application/json

{
  "code": "QR_CODE_STRING"
}
```

**Response:**
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

## 📦 Tech Stack

### Backend
- **Laravel 12** - PHP Framework (Latest)
- **PHP 8.2.12** - Programming Language
- **SQLite** - Database (Development)
- **Spatie Laravel Permission** - Role & Permission Management
- **Laravel Octane** - High-performance server (Optional)

### Frontend
- **Filament v3** - Admin Panel Framework (TALL Stack)
- **Livewire 3** - Full-stack framework
- **Alpine.js** - Lightweight JavaScript
- **Tailwind CSS** - Utility-first CSS
- **Blade Templates** - Laravel templating

### UI/UX Libraries
- **SweetAlert2** - Beautiful alerts & modals
- **Chart.js** - Data visualization
- **QR Code Scanner** - HTML5 QR scanning
- **Heroicons** - Icon library

### Additional Tools
- **Maatwebsite Excel** - Excel import/export
- **Barryvdh DomPDF** - PDF generation
- **Intervention Image** - Image processing
- **Carbon** - Date/time manipulation

### Development Tools
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager
- **Vite** - Frontend build tool
- **PHPUnit** - Testing framework

## 📚 Documentation

### Panduan Pengguna
- [PANDUAN_PENGGUNAAN.md](PANDUAN_PENGGUNAAN.md) - Panduan lengkap
- [PANDUAN_ABSENSI_SISWA.md](PANDUAN_ABSENSI_SISWA.md) - Panduan siswa
- [PANDUAN_ADMIN_GURU_VERIFIKASI.md](PANDUAN_ADMIN_GURU_VERIFIKASI.md) - Panduan verifikasi
- [CARA_IMPORT_EXCEL.md](CARA_IMPORT_EXCEL.md) - Import data Excel

### Fitur Lengkap
- [FITUR_MANAJEMEN_ABSENSI_LENGKAP.md](FITUR_MANAJEMEN_ABSENSI_LENGKAP.md) - Sistem absensi
- [FITUR_MANAJEMEN_USER_LENGKAP.md](FITUR_MANAJEMEN_USER_LENGKAP.md) - Manajemen user
- [FITUR_PENGUMUMAN_LENGKAP.md](FITUR_PENGUMUMAN_LENGKAP.md) - Sistem pengumuman
- [FITUR_WELCOME_GREETING.md](FITUR_WELCOME_GREETING.md) - Welcome widget
- [SWEETALERT_INTEGRATION.md](SWEETALERT_INTEGRATION.md) - SweetAlert2

### Portal Siswa
- [STUDENT_PORTAL_COMPLETE.md](STUDENT_PORTAL_COMPLETE.md) - Portal siswa lengkap
- [CARA_AKSES_PORTAL_SISWA.md](CARA_AKSES_PORTAL_SISWA.md) - Cara akses
- [FITUR_ABSEN_MANUAL.md](FITUR_ABSEN_MANUAL.md) - Absensi manual

### Sistem Lanjutan
- [SISTEM_DOUBLE_VERIFICATION.md](SISTEM_DOUBLE_VERIFICATION.md) - Double verification
- [REALTIME_POLLING_COMPLETE.md](REALTIME_POLLING_COMPLETE.md) - Real-time polling
- [QR_CODE_FEATURE.md](QR_CODE_FEATURE.md) - QR Code system

### Status & Testing
- [STATUS_FINAL_LENGKAP.md](STATUS_FINAL_LENGKAP.md) - Status lengkap
- [TESTING_COMPLETE.md](TESTING_COMPLETE.md) - Testing guide
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Deployment

## 🎯 Real-Time Features

| Feature | Method | Interval |
|---------|--------|----------|
| Dashboard Stats | Polling | 30s |
| Absensi Chart | Polling | 60s |
| Rekap Widgets | Polling | 120s |
| Absensi Table | Polling | 30s |
| Notifications | Polling | 30s |
| Pengajuan Izin | Polling | 30s |
| Dashboard Wali Kelas | Polling | 30s |
| Laporan Harian | Polling | 30s |
| Input Absensi Kelas | Polling | 30s |

## 🔧 Configuration

### Environment (.env)
```env
APP_NAME="Sistem Absensi Sekolah"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite

FILAMENT_FILESYSTEM_DISK=public
```

### Default Users
```
Admin:
- Email: admin@example.com
- Password: password

Guru:
- Email: guru@example.com
- Password: password

Murid:
- Email: murid@example.com
- Password: password
```

## 🐛 Troubleshooting

### Widget tidak muncul?
```bash
php artisan optimize:clear
php artisan view:clear
```

### Notifikasi tidak muncul?
1. Logout dan login kembali
2. Hard refresh browser (Ctrl+F5)
3. Cek di incognito window
4. Pastikan polling aktif (30s)

### Assets tidak update?
```bash
npm run build
php artisan view:clear
php artisan optimize:clear
```

### Permission error?
```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### QR Scanner tidak jalan?
1. Pastikan HTTPS atau localhost
2. Allow camera permission
3. Gunakan browser modern (Chrome/Firefox)

## 🚀 Performance Tips

1. **Enable Octane** untuk performa maksimal
2. **Use Redis** untuk cache & session di production
3. **Optimize images** sebelum upload
4. **Enable OPcache** di PHP
5. **Use CDN** untuk assets static

## 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## � Keey Features Highlight

- ✅ **Multi-Panel System** - Admin, Guru, dan Siswa punya panel sendiri
- ✅ **Auto-Create Users** - User otomatis dibuat saat tambah Murid/Guru
- ✅ **Double Verification** - Sistem verifikasi 2 langkah untuk absensi
- ✅ **QR Code System** - Scan QR untuk absensi cepat
- ✅ **Real-time Notifications** - Notifikasi langsung dengan polling
- ✅ **Welcome Greeting** - Sapaan dinamis sesuai waktu
- ✅ **SweetAlert2** - Alert cantik dan interaktif
- ✅ **Excel Import/Export** - Import data massal dengan mudah
- ✅ **PDF Reports** - Laporan profesional dalam PDF
- ✅ **Responsive Design** - Mobile-friendly
- ✅ **SPA Mode** - Navigasi cepat tanpa reload
- ✅ **Role-Based Access** - Keamanan dengan permission

## 🎉 Credits

Built with ❤️ using:
- [Laravel 12](https://laravel.com)
- [Filament v3](https://filamentphp.com)
- [Livewire 3](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [SweetAlert2](https://sweetalert2.github.io)
- [Alpine.js](https://alpinejs.dev)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
