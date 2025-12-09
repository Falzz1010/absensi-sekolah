# Quick Guide: Auto-Create Murid dari User

## Untuk Admin

### Cara Membuat Siswa Baru (User + Murid Sekaligus)

1. **Login sebagai Admin**

2. **Buka Menu Users**
   - Sidebar → Manajemen User → Users

3. **Klik Create**

4. **Isi Form:**
   - **Nama**: Nama lengkap siswa (contoh: "Ahmad Rizki")
   - **Email**: Email siswa (contoh: "ahmad.rizki@sekolah.com")
   - **Password**: Password untuk login (contoh: "password123")
   - **Role**: Pilih **"murid"** (wajib!)
   - **Kelas**: Pilih kelas siswa (opsional, bisa diatur nanti)

5. **Klik Create**

6. **Selesai!** ✅
   - User account dibuat
   - Record Murid otomatis dibuat
   - Siswa bisa login ke Student Portal
   - Siswa muncul di dropdown Create Absensi

### Cara Update Data Siswa

1. **Buka Menu Users → Edit**

2. **Ubah data yang diperlukan:**
   - Nama → Otomatis update di Murid
   - Email → Otomatis update di Murid
   - Kelas → Otomatis update di Murid

3. **Klik Save**

4. **Selesai!** Data tersinkronisasi ✅

### Cara Nonaktifkan Siswa

**Opsi 1: Ubah Role**
1. Buka Users → Edit siswa
2. Ubah role dari "murid" ke role lain (atau hapus role murid)
3. Save
4. Record Murid otomatis nonaktif (is_active = false)

**Opsi 2: Hapus User**
1. Buka Users → Delete siswa
2. Confirm
3. Record Murid otomatis nonaktif (data historis tetap ada)

## FAQ

### Q: Apakah harus mengisi kelas saat membuat User?
**A:** Tidak wajib. Kelas bisa dikosongkan dan diatur nanti di menu Murid atau saat edit User.

### Q: Bagaimana jika siswa sudah punya User tapi belum punya Murid?
**A:** Edit User tersebut dan save ulang. Observer akan otomatis membuat record Murid.

### Q: Apakah data absensi hilang jika User dihapus?
**A:** Tidak. Record Murid hanya di-nonaktifkan (is_active = false), data absensi tetap tersimpan.

### Q: Bagaimana cara mengaktifkan kembali siswa yang sudah nonaktif?
**A:** 
1. Buka menu Murid → Edit siswa yang nonaktif
2. Set is_active = true
3. Pastikan User masih ada dan punya role murid

### Q: Apakah bisa import Excel User dan auto-create Murid?
**A:** Saat ini belum. Fitur import Excel masih terpisah untuk User dan Murid. Bisa ditambahkan nanti jika diperlukan.

### Q: Kenapa siswa tidak muncul di dropdown Create Absensi?
**A:** Pastikan:
- User punya role "murid"
- Record Murid sudah dibuat (cek di menu Murid)
- Murid.is_active = true
- Murid.user_id terisi

### Q: Apakah bisa satu User punya multiple role (murid + admin)?
**A:** Secara teknis bisa, tapi tidak disarankan. Sistem dirancang untuk:
- Admin/Guru → Admin Panel
- Murid → Student Panel
- Jika User punya role murid + admin, akan ada konflik akses panel

## Tips

💡 **Best Practice:**
- Gunakan email sekolah untuk siswa (contoh: nama@sekolah.com)
- Password minimal 8 karakter
- Atur kelas saat membuat User agar langsung lengkap
- Jangan hapus User kecuali benar-benar diperlukan (gunakan nonaktif)

💡 **Workflow Efisien:**
1. Import Excel User dengan role murid → Auto-create Murid (jika fitur sudah ada)
2. Atau create User satu per satu dengan kelas langsung terisi
3. Siswa langsung bisa login dan absen

💡 **Troubleshooting:**
- Jika siswa tidak bisa login → Cek User ada dan password benar
- Jika siswa tidak muncul di dropdown → Cek role murid dan Murid record
- Jika kelas tidak tersimpan → Edit User dan pilih kelas lagi

## Ringkasan

✅ **1 Langkah = 2 Record**
- Create User dengan role murid
- Murid record otomatis dibuat

✅ **Auto-Sync**
- Update User → Murid ikut update
- Hapus User → Murid nonaktif

✅ **Konsisten**
- Sama seperti Guru yang terhubung dengan User
- Semua data terpusat di User management

✅ **Aman**
- Data historis tidak hilang
- Absensi tetap tersimpan
