# Fitur Auto-Create Murid dari User

## Deskripsi
Sistem sekarang otomatis membuat record Murid ketika Admin membuat User dengan role "murid" atau "student".

## Cara Kerja

### 1. Membuat User Baru dengan Role Murid
Ketika Admin membuat User baru di menu **Users**:
1. Isi form:
   - **Nama**: Nama siswa
   - **Email**: Email siswa
   - **Password**: Password untuk login
   - **Role**: Pilih "murid" atau "student"
   - **Kelas**: (Opsional) Pilih kelas siswa

2. Klik **Create**

3. Sistem otomatis:
   - Membuat User account
   - Membuat record Murid yang terhubung ke User
   - Mengisi data Murid dengan nama dan email dari User
   - Mengatur kelas jika dipilih

### 2. Update User yang Sudah Ada
Ketika Admin mengupdate User:
1. Jika role diubah menjadi "murid", sistem otomatis membuat record Murid
2. Jika nama/email diubah, record Murid juga ikut terupdate
3. Jika kelas diubah, record Murid juga ikut terupdate
4. Jika role diubah dari "murid" ke role lain, record Murid di-nonaktifkan (is_active = false)

### 3. Hapus User
Ketika Admin menghapus User dengan role murid:
- Record Murid tidak dihapus, tapi di-nonaktifkan (is_active = false)
- Data absensi tetap tersimpan untuk keperluan historis

## Keuntungan

✅ **Tidak Ada Data Dummy**: Dropdown di Create Absensi hanya menampilkan murid yang punya akun User

✅ **Sinkronisasi Otomatis**: Nama dan email selalu sinkron antara User dan Murid

✅ **Mudah Dikelola**: Admin cukup kelola User, record Murid otomatis dibuat

✅ **Konsisten**: Sama seperti Guru yang sudah terhubung dengan User

## Implementasi Teknis

### File yang Dibuat/Diubah:
1. **app/Observers/UserObserver.php** - Observer untuk auto-create Murid
2. **app/Providers/AppServiceProvider.php** - Register UserObserver
3. **app/Filament/Resources/UserResource.php** - Tambah field kelas_id
4. **app/Filament/Resources/UserResource/Pages/CreateUser.php** - Handle kelas saat create
5. **app/Filament/Resources/UserResource/Pages/EditUser.php** - Handle kelas saat edit

### Database:
- Tabel `murids` sudah punya field `user_id` (foreign key ke `users`)
- Field `is_active` untuk menandai murid aktif/nonaktif

## Testing

### Test 1: Create User Baru dengan Role Murid
```
1. Login sebagai Admin
2. Buka menu Users → Create
3. Isi form:
   - Nama: "Test Siswa"
   - Email: "test@siswa.com"
   - Password: "password123"
   - Role: Pilih "murid"
   - Kelas: Pilih kelas (opsional)
4. Klik Create
5. Cek menu Murid → Seharusnya ada "Test Siswa" dengan user_id terisi
6. Cek menu Absensi → Create → Dropdown Murid seharusnya menampilkan "Test Siswa"
```

### Test 2: Update User yang Sudah Ada
```
1. Login sebagai Admin
2. Buka menu Users → Edit user yang sudah ada
3. Ubah nama menjadi "Test Siswa Updated"
4. Klik Save
5. Cek menu Murid → Nama seharusnya ikut berubah
```

### Test 3: Ubah Role dari Murid ke Admin
```
1. Login sebagai Admin
2. Buka menu Users → Edit user dengan role murid
3. Ubah role dari "murid" ke "admin"
4. Klik Save
5. Cek menu Murid → Record masih ada tapi is_active = false
```

## Catatan Penting

⚠️ **Field Kelas Opsional**: Saat membuat User, field kelas bisa dikosongkan. Admin bisa mengatur kelas nanti di menu Murid.

⚠️ **Data Historis**: Record Murid tidak pernah dihapus permanen untuk menjaga data absensi historis.

⚠️ **Role Name**: Sistem mendukung role name "murid" atau "student" (keduanya valid).

## Troubleshooting

### Murid Tidak Muncul di Dropdown Create Absensi
**Penyebab**: User tidak punya role "murid" atau record Murid belum dibuat

**Solusi**:
1. Cek di menu Users → Pastikan user punya role "murid"
2. Cek di menu Murid → Pastikan ada record dengan user_id terisi
3. Jika belum ada, edit User dan save ulang (Observer akan auto-create)

### Kelas Tidak Tersimpan
**Penyebab**: Kelas dipilih tapi tidak tersimpan di record Murid

**Solusi**:
1. Edit User lagi
2. Pilih kelas
3. Save
4. Cek di menu Murid → kelas_id seharusnya terisi

### Error "User already has Murid record"
**Penyebab**: Observer mencoba membuat Murid yang sudah ada

**Solusi**: Observer sudah handle ini dengan check `exists()` sebelum create. Jika masih error, cek log Laravel.
