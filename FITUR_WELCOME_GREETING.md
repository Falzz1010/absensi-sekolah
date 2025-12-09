# ✅ Fitur Welcome Greeting di Dashboard

## 🎯 Overview

Setiap dashboard (Admin, Guru, Siswa) sekarang menampilkan greeting personal dengan nama user dan informasi tambahan.

## 📱 Fitur

### 1. Greeting Dinamis
Menyesuaikan waktu:
- **Pagi** (00:00 - 11:59): "Selamat Pagi"
- **Siang** (12:00 - 14:59): "Selamat Siang"
- **Sore** (15:00 - 17:59): "Selamat Sore"
- **Malam** (18:00 - 23:59): "Selamat Malam"

### 2. Informasi Personal
- **Nama User**: Ditampilkan dari auth()->user()->name
- **Role/Kelas**: 
  - Admin/Guru: Menampilkan role (Administrator/Guru)
  - Siswa: Menampilkan kelas (contoh: "Kelas X IPA 1")
- **Tanggal**: Format Indonesia lengkap (contoh: "Senin, 9 Desember 2025")

### 3. Design
- Gradient background yang cantik
- Responsive untuk mobile dan desktop
- Icon yang sesuai dengan role
- Decorative elements untuk estetika

## 🎨 Tampilan

### Admin/Guru Dashboard
```
┌─────────────────────────────────────────────────┐
│  Selamat Pagi, Administrator! 👋                │
│  👤 Administrator                                │
│  📅 Senin, 9 Desember 2025                      │
│                                                  │
│  Selamat datang di Sistem Absensi Sekolah.     │
│  Semoga hari Anda menyenangkan! 🎓             │
└─────────────────────────────────────────────────┘
```

### Student Dashboard
```
┌─────────────────────────────────────────────────┐
│  Selamat Pagi, Andi! 👋                         │
│  🎓 Kelas X IPA 1                               │
│  📅 Senin, 9 Desember 2025                      │
│                                                  │
│  Jangan lupa untuk melakukan absensi hari ini! │
│  Semangat belajar! 📚                           │
└─────────────────────────────────────────────────┘
```

## 📂 File yang Dibuat

### 1. Admin/Guru Widget
- **Widget**: `app/Filament/Widgets/WelcomeWidget.php`
- **View**: `resources/views/filament/widgets/welcome-widget.blade.php`
- **Color**: Gradient primary (amber/orange)

### 2. Student Widget
- **Widget**: `app/Filament/Student/Widgets/WelcomeWidget.php`
- **View**: `resources/views/filament/student/widgets/welcome-widget.blade.php`
- **Color**: Gradient blue/indigo

## 🔧 Konfigurasi

### Sort Order
Widget ini memiliki `sort = -1` sehingga selalu tampil paling atas di dashboard.

### Column Span
Widget menggunakan `columnSpan = 'full'` untuk mengambil lebar penuh.

## 🎬 Cara Kerja

### 1. Greeting Logic
```php
public function getGreeting(): string
{
    $hour = now()->hour;
    
    if ($hour < 12) return 'Selamat Pagi';
    elseif ($hour < 15) return 'Selamat Siang';
    elseif ($hour < 18) return 'Selamat Sore';
    else return 'Selamat Malam';
}
```

### 2. User Name
```php
public function getUserName(): string
{
    return auth()->user()->name;
}
```

### 3. Role Detection (Admin/Guru)
```php
public function getUserRole(): string
{
    $roles = auth()->user()->roles->pluck('name')->toArray();
    
    if (in_array('admin', $roles)) return 'Administrator';
    elseif (in_array('guru', $roles)) return 'Guru';
    elseif (in_array('murid', $roles)) return 'Siswa';
    
    return 'User';
}
```

### 4. Class Detection (Student)
```php
public function getUserClass(): string
{
    $murid = auth()->user()->murid;
    return $murid ? $murid->kelas : '-';
}
```

### 5. Date Formatting
```php
public function getCurrentDate(): string
{
    return now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
}
```

## 🎨 Customization

### Ubah Warna Gradient
```php
// Admin/Guru (Orange)
bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700

// Student (Blue)
bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700
```

### Ubah Pesan
Edit di file blade:
```blade
<p class="text-white/80 text-sm">
    Pesan custom Anda di sini
</p>
```

### Tambah Informasi
Tambahkan method baru di widget:
```php
public function getCustomInfo(): string
{
    return 'Info tambahan';
}
```

Lalu tampilkan di blade:
```blade
<p>{{ $this->getCustomInfo() }}</p>
```

## 📱 Responsive

Widget sudah responsive:
- **Desktop**: Icon ditampilkan di kanan
- **Mobile**: Icon disembunyikan untuk menghemat space
- **Tablet**: Layout menyesuaikan

## ✅ Status

- ✅ WelcomeWidget untuk Admin/Guru created
- ✅ WelcomeWidget untuk Student created
- ✅ Registered di AdminPanelProvider
- ✅ Registered di StudentPanelProvider
- ✅ Greeting dinamis berdasarkan waktu
- ✅ Nama user ditampilkan
- ✅ Role/Kelas ditampilkan
- ✅ Tanggal Indonesia format
- ✅ Responsive design
- ✅ Ready to use!

Sekarang setiap user akan disambut dengan greeting personal saat membuka dashboard! 👋
