# Penjelasan Dashboard dan Panel

## 🎯 Perbedaan Panel Admin dan Panel Murid

### 1️⃣ PANEL ADMIN (Warna Kuning/Amber) - `/admin`
**Untuk:** Admin dan Guru  
**Login:** `admin@example.com` atau `guru@example.com`

**Menu yang tersedia:**
- ✅ **Dashboard** - Dashboard utama admin
- ✅ **Dashboard Overview** - Statistik lengkap sekolah
- ✅ **Dashboard Wali Kelas** - Khusus untuk wali kelas
- ✅ **Data Murid** - CRUD management data siswa (tambah/edit/hapus)
- ✅ **Data Guru** - Management data guru
- ✅ **Data Kelas** - Management kelas
- ✅ **Absensi** - Input dan kelola absensi
- ✅ **Laporan** - Export Excel/PDF
- ✅ **Pengaturan** - Konfigurasi sistem

### 2️⃣ PANEL MURID (Warna Biru) - `/student`
**Untuk:** Siswa/Murid  
**Login:** `murid@example.com` (atau email murid lainnya)

**Menu yang tersedia:**
- ✅ **Dashboard** - Dashboard pribadi murid
- ✅ **Scan QR** - Scan QR code untuk absen
- ✅ **Ajukan Izin** - Upload bukti sakit/izin
- ✅ **Riwayat Absensi** - Lihat history kehadiran
- ✅ **Profil** - Update foto dan info pribadi

---

## ❓ Pertanyaan Umum

### Q: Kenapa ada "Data Murid" di panel admin?
**A:** "Data Murid" di panel admin adalah untuk **MANAGEMENT** - admin/guru bisa:
- Tambah murid baru
- Edit data murid
- Hapus murid
- Import dari Excel
- Pindah kelas

Ini BERBEDA dengan "Dashboard Murid" di panel siswa yang hanya untuk melihat data pribadi.

### Q: Apakah murid bisa akses panel admin?
**A:** TIDAK! Murid hanya bisa akses `/student` (panel biru). Sudah ada security check di `User.php`:
```php
if ($panel->getId() === 'admin') {
    return $this->hasAnyRole(['admin', 'guru']);
}
```

### Q: Apakah admin/guru bisa akses panel murid?
**A:** TIDAK! Admin dan guru hanya bisa akses `/admin` (panel kuning). Security check:
```php
if ($panel->getId() === 'student') {
    return $this->hasRole('student') 
        && !$this->hasAnyRole(['admin', 'guru']);
}
```

---

## 🔐 Cara Test Security

### Test 1: Murid tidak bisa akses admin
```bash
# Login sebagai murid
Email: murid@example.com
Password: password

# Coba akses: http://localhost/admin
# Hasil: Redirect ke /student atau error 403
```

### Test 2: Admin tidak bisa akses student panel
```bash
# Login sebagai admin
Email: admin@example.com
Password: password

# Coba akses: http://localhost/student
# Hasil: Error 403 atau redirect ke /admin
```

---

## ✅ Status Saat Ini

| Fitur | Status | Keterangan |
|-------|--------|------------|
| Panel Admin terpisah | ✅ | `/admin` - kuning |
| Panel Murid terpisah | ✅ | `/student` - biru |
| Security role-based | ✅ | Sudah ada di User.php |
| Dashboard tidak duplikat | ✅ | Fixed di AdminPanelProvider |
| Routes terpisah | ✅ | Verified dengan route:list |

---

## 🎨 Visual Perbedaan

```
┌─────────────────────────────────────────┐
│  PANEL ADMIN (Kuning) - /admin         │
├─────────────────────────────────────────┤
│  👤 Admin / Guru                        │
│  📊 Dashboard Overview                  │
│  👥 Data Murid (CRUD Management)       │
│  👨‍🏫 Data Guru                           │
│  📚 Data Kelas                          │
│  ✅ Input Absensi                       │
│  📄 Laporan                             │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│  PANEL MURID (Biru) - /student         │
├─────────────────────────────────────────┤
│  🧑‍🎓 Murid/Siswa                         │
│  🏠 Dashboard (Personal)                │
│  📱 Scan QR Code                        │
│  📝 Ajukan Izin/Sakit                   │
│  📊 Riwayat Absensi                     │
│  👤 Profil Saya                         │
└─────────────────────────────────────────┘
```

---

## 🔧 File Penting

1. **app/Models/User.php** - Security check `canAccessPanel()`
2. **app/Providers/Filament/AdminPanelProvider.php** - Config panel admin
3. **app/Providers/Filament/StudentPanelProvider.php** - Config panel murid
4. **app/Filament/Resources/MuridResource.php** - CRUD management murid (admin)
5. **app/Filament/Student/Pages/StudentDashboard.php** - Dashboard pribadi murid

---

## 📝 Kesimpulan

**TIDAK ADA DUPLIKASI!** Yang ada adalah:
- **Panel Admin**: Untuk management/kelola data (termasuk kelola data murid)
- **Panel Murid**: Untuk siswa lihat data pribadi mereka sendiri

Ini adalah **2 sistem berbeda** dengan **tujuan berbeda** dan **user berbeda**.
